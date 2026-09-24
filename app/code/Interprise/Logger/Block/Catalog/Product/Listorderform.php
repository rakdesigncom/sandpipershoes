<?php
namespace Interprise\Logger\Block\Catalog\Product;

use Magento\Framework\ObjectManagerInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Block\Product\ProductList\Toolbar;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Config;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Config\Element;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\Render;
use Magento\Framework\Url\Helper\Data;
use Magento\Catalog\Block\Product\Context;

class Listorderform extends \Interprise\Logger\Block\Catalog\Product\ListProduct
{

    public function __construct(
        Context $context,
        PostHelper $postDataHelper,
        Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        Data $urlHelper,
        \Magento\Customer\Model\Session $session,
        \Interprise\Logger\Helper\Data $ihelper,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $collectionnew
    ) {
        $this->_catalogLayer = $layerResolver->get();
        $this->_postDataHelper = $postDataHelper;
        $this->categoryRepository = $categoryRepository;
        $this->urlHelper = $urlHelper;
        $this->_ihelper = $ihelper;
        $this->_session = $session;
        $this->collectoinnew = $collectionnew;
        parent::__construct(
            $context,
            $postDataHelper,
            $layerResolver,
            $categoryRepository,
            $urlHelper,
            $session,
            $ihelper
        );
    }

    public function getLoadedProductCollection()
    {
        return $this->_getProductCollection();
    }
    protected function _getProductCollection()
    {
        if ($this->_productCollection === null) {
            $this->_productCollection = $this->initializeProductCollection();
        }

        return $this->_productCollection;
    }
    private function initializeProductCollection()
    {
        $layer = $this->getLayer();
        /* @var $layer Layer */
        if ($this->getShowRootCategory()) {
            $this->setCategoryId($this->_storeManager->getStore()->getRootCategoryId());
        }

        // if this is a product view page
        if ($this->_coreRegistry->registry('product')) {
            // get collection of categories this product is associated with
            $categories = $this->_coreRegistry->registry('product')
                ->getCategoryCollection()->setPage(1, 1)
                ->load();
            // if the product is associated with any category
            if ($categories->count()) {
                // show products from this category
                $this->setCategoryId(current($categories->getIterator())->getId());
            }
        }

        $origCategory = null;
        if ($this->getCategoryId()) {
            try {
                $category = $this->categoryRepository->get($this->getCategoryId());
            } catch (NoSuchEntityException $e) {
                $category = null;
            }

            if ($category) {
                $origCategory = $layer->getCurrentCategory();
                $layer->setCurrentCategory($category);
            }
        }
        $cond ='10';
//        if (isset($_REQUEST['item']) && $_REQUEST['item']!='') {
//            $cond = $_REQUEST['item'];
//        }
  
        if (null !== $this->getRequest()->getParam('item') && $this->getRequest()->getParam('item')!='') {
            $cond=$this->getRequest()->getParam('item');
        }

        $collection = $layer->getProductCollection();
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
        //$productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection');
        $productCollection = $this->collectoinnew->create();
        /** Apply filters here */
        $collection = $productCollection->addAttributeToSelect('*');
                 $collection->addAttributeToFilter(
                     [
                           [
                               'attribute' => 'name',
                               'like' => "%$cond%"
                           ],
                           [
                               'attribute' => 'sku',
                               'like' => "%$cond%"
                           ]
                       ]
                 )->load();

        $this->prepareSortableFieldsByCategory($layer->getCurrentCategory());

        if ($origCategory) {
            $layer->setCurrentCategory($origCategory);
        }
        
        $this->_eventManager->dispatch(
            'catalog_block_product_list_collection',
            ['collection' => $collection]
        );

        return $collection;
    }
}

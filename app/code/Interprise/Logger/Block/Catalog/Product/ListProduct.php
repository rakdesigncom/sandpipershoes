<?php
namespace Interprise\Logger\Block\Catalog\Product;

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

class ListProduct extends \Magento\Catalog\Block\Product\ListProduct
{
    public function __construct(
        Context $context,
        PostHelper $postDataHelper,
        Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        Data $urlHelper,
        \Magento\Customer\Model\Session $session,
        \Interprise\Logger\Helper\Data $ihelper
    ) {
        $this->_catalogLayer = $layerResolver->get();
        $this->_postDataHelper = $postDataHelper;
        $this->categoryRepository = $categoryRepository;
        $this->urlHelper = $urlHelper;
        $this->_ihelper = $ihelper;
        $this->_session = $session;
        parent::__construct(
            $context,
            $postDataHelper,
            $layerResolver,
            $categoryRepository,
            $urlHelper
        );
    }
    
    public function getProductDetailsHtml(\Magento\Catalog\Model\Product $product)
    {
//         $customerSession = $this->_session;
//         $itemcode = $product->getInterpriseItemCode();
//        $is_pricinglevel = $customerSession->getISPricinglevel();
//        $this->_dhelper = $this->_ihelper->create();
//        $lowerprice = $this->_dhelper->getLowestTierprice($itemcode, $is_pricinglevel);
//        $html = $this->getLayout()->createBlock('Magento\Framework\View\Element\Template')->setProduct($product)->setLowestprise($lowerprice)->setTemplate('Interprise_Logger::listingtierprice.phtml')->toHtml();
        $renderer = $this->getDetailsRenderer($product->getTypeId());
        if ($renderer) {
            $renderer->setProduct($product);
            return $renderer->toHtml();
        }
        return '';
    }
}

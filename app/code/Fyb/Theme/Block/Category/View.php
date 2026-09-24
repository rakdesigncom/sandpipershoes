<?php

namespace Fyb\Theme\Block\Category;

class View extends \Magento\Catalog\Block\Category\View
{
    private $catalogData;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Helper\Category $categoryHelper,
        array $data = [],
        \Magento\Catalog\Helper\Data $catalogData = null
    ) {
        parent::__construct($context, $layerResolver, $registry, $categoryHelper, $data, $catalogData);

        $this->catalogData = $catalogData ?? \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Catalog\Helper\Data::class);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $block = $this->getLayout()->createBlock(\Magento\Catalog\Block\Breadcrumbs::class);

        $category = $this->getCurrentCategory();
        if ($category) {
            $title = $category->getMetaTitle();
            if ($title) {
                $this->pageConfig->getTitle()->set($title);
            } else {
                $title = [];
                foreach ($this->catalogData->getBreadcrumbPath() as $breadcrumb) {
                    $title[] = $breadcrumb['label'];
                }
                $this->pageConfig->getTitle()->set(join($block->getTitleSeparator(), array_reverse($title)));
            }
            $description = $category->getMetaDescription();
            if ($description) {
                $this->pageConfig->setDescription($description);
            }
            $keywords = $category->getMetaKeywords();
            if ($keywords) {
                $this->pageConfig->setKeywords($keywords);
            }
            if ($this->_categoryHelper->canUseCanonicalTag()) {
                $this->pageConfig->getAssetCollection()->remove($category->getUrl());
                $this->pageConfig->addRemotePageAsset(
                    trim($category->getUrl(), '/') . $this->getCanonicalQuery(),
                    'canonical',
                    ['attributes' => ['rel' => 'canonical']]
                );
            }

            $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
            if ($pageMainTitle) {
                $pageMainTitle->setPageTitle($this->getCurrentCategory()->getName());
            }
        }

        return $this;
    }

    protected function getCanonicalQuery()
    {
        $pageParam = '';
        if ($this->getRequest()->getParam('p')) {
            $pageParam = 'p';
        }

        if ($this->getRequest()->getParam($pageParam) == 1) {
            $pageParam = '';
        }

        $additionalQuery = '';
        if ($pageParam) {
            $additionalQuery = '?' . $pageParam . '=' . $this->getRequest()->getParam($pageParam);
        }

        return $additionalQuery;
    }
}

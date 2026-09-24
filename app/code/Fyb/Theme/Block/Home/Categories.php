<?php

namespace Fyb\Theme\Block\Home;

class Categories extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Store\Model\Information $storeInfo
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        array $data = []
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->categoryFactory = $categoryFactory;

        parent::__construct($context, $data);
    }

    public function getCategories()
    {
        $parent = $this->_storeManager->getStore()->getRootCategoryId();
        $collection = $this->categoryFactory->create()->getCategories($parent, 1, false, true, false);
        $collection->addAttributeToSelect('image')
            ->setOrder('position', 'ASC');

        return $collection;
    }

    public function getCategoryImage($category)
    {
        return $category->getImageUrl();
    }
}


<?php

namespace Fyb\RequestCatalogue\Block;

use Magento\Framework\View\Element\Template;

class Form extends Template
{
    /**
     * @var \Magento\Directory\Block\Data
     */
    protected $directoryBlock;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Directory\Block\Data $directoryBlock
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Block\Data $directoryBlock,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->directoryBlock = $directoryBlock;
    }

    public function getFormAction()
    {
        return $this->getUrl('request-catalogue/index/post', ['_secure' => true]);
    }

    public function getCountryHtmlSelect()
    {
        return $this->directoryBlock->getCountryHtmlSelect();
    }
}

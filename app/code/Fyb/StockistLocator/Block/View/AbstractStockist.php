<?php

namespace Fyb\StockistLocator\Block\View;

class AbstractStockist extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->coreRegistry = $coreRegistry;
    }

    public function getStockist()
    {
        return $this->coreRegistry->registry('current_stockist');
    }

    public function getAddressFormated()
    {
        $stockist = $this->getStockist();
        $address = array_filter([
            $stockist->getName(),
            $stockist->getData('address_1'),
            $stockist->getData('address_2'),
            $stockist->getCounty(),
            $stockist->getCity(),
            $stockist->getPostcode(),
        ]);

        return implode('<br/>', $address);
    }
}

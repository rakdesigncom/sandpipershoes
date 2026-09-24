<?php

namespace Interprise\CustomShippingRates\Model;

class CustomShipping extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('Interprise\CustomShippingRates\Model\ResourceModel\CustomShipping');
    }
}
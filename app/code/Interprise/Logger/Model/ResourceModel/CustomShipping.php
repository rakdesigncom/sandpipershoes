<?php

namespace Interprise\Logger\Model\ResourceModel;

class CustomShipping extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('custom_shipping_rates', 'id');
    }
}
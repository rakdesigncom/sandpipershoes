<?php

namespace Interprise\Logger\Model;

class CustomShipping extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('Interprise\Logger\Model\ResourceModel\CustomShipping');
    }
}
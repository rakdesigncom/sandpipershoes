<?php

namespace Interprise\Logger\Model\ResourceModel\CustomShipping;

class Collection
    extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init(
            'Interprise\Logger\Model\CustomShipping',
            'Interprise\Logger\Model\ResourceModel\CustomShipping'
        );
    }
}
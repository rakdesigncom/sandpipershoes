<?php

namespace Interprise\Logger\Model\ResourceModel\OrderLog;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @inheirtdoc
     */
    protected function _construct()
    {
        $this->_init(
            \Interprise\Logger\Model\OrderLog::class,
            \Interprise\Logger\Model\ResourceModel\OrderLog::class
        );
    }

}

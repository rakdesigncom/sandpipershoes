<?php


namespace Interprise\Logger\Model\ResourceModel\FailedOrders;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected $_idFieldName = 'failedorder_id';
    protected function _construct()
    {
        $this->_init(
            \Interprise\Logger\Model\FailedOrders::class,
            \Interprise\Logger\Model\ResourceModel\FailedOrders::class
        );
    }
}

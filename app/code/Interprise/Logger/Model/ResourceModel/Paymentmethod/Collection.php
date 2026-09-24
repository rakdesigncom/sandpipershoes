<?php


namespace Interprise\Logger\Model\ResourceModel\Paymentmethod;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Interprise\Logger\Model\Paymentmethod::class,
            \Interprise\Logger\Model\ResourceModel\Paymentmethod::class
        );
    }
}

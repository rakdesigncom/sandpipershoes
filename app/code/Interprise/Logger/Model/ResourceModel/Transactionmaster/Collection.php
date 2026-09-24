<?php


namespace Interprise\Logger\Model\ResourceModel\Transactionmaster;

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
            \Interprise\Logger\Model\Transactionmaster::class,
            \Interprise\Logger\Model\ResourceModel\Transactionmaster::class
        );
    }
}

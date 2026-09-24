<?php


namespace Interprise\Logger\Model\ResourceModel\Custompaymentitem;

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
            \Interprise\Logger\Model\Custompaymentitem::class,
            \Interprise\Logger\Model\ResourceModel\Custompaymentitem::class
        );
    }
}

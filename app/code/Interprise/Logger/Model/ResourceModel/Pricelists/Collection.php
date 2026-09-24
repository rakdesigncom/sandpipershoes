<?php


namespace Interprise\Logger\Model\ResourceModel\Pricelists;

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
            \Interprise\Logger\Model\Pricelists::class,
            \Interprise\Logger\Model\ResourceModel\Pricelists::class
        );
    }
}

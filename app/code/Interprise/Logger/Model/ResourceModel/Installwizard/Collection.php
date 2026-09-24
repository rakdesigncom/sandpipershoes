<?php


namespace Interprise\Logger\Model\ResourceModel\Installwizard;

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
            \Interprise\Logger\Model\Installwizard::class,
            \Interprise\Logger\Model\ResourceModel\Installwizard::class
        );
    }
}

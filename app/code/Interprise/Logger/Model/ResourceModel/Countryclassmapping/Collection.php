<?php


namespace Interprise\Logger\Model\ResourceModel\Countryclassmapping;

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
            \Interprise\Logger\Model\Countryclassmapping::class,
            \Interprise\Logger\Model\ResourceModel\Countryclassmapping::class
        );
    }
}

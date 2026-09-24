<?php


namespace Interprise\Logger\Model\ResourceModel\CronActivitySchedule;

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
            \Interprise\Logger\Model\CronActivitySchedule::class,
            \Interprise\Logger\Model\ResourceModel\CronActivitySchedule::class
        );
    }
}

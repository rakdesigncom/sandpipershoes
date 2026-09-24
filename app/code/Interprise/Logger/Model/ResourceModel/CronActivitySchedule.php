<?php


namespace Interprise\Logger\Model\ResourceModel;

class CronActivitySchedule extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('interprise_logger_cronactivityschedule', 'cronactivityschedule_id');
    }
}

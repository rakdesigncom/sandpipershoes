<?php


namespace Interprise\Logger\Model\ResourceModel;

class ReattemptFrequency extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('interprise_reattempt_frequency', 'reattempt_id');
    }
}

<?php


namespace Interprise\Logger\Model\ResourceModel;

class Changelog extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('interprise_logger_changelog', 'changelog_id');
    }
}

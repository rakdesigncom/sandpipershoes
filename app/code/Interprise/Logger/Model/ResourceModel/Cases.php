<?php


namespace Interprise\Logger\Model\ResourceModel;

class Cases extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('interprise_case', 'entity_id');
    }
}

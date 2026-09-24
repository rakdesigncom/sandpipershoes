<?php


namespace Interprise\Logger\Model\ResourceModel;

class Statementaccount extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('interprise_statement_account', 'statement_id');
    }
}

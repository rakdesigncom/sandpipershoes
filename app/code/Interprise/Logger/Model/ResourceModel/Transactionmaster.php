<?php


namespace Interprise\Logger\Model\ResourceModel;

class Transactionmaster extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('interprise_transaction_master', 'id');
    }
}

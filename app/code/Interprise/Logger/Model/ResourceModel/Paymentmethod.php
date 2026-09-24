<?php


namespace Interprise\Logger\Model\ResourceModel;

class Paymentmethod extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('interprise_payment_methods', 'id');
    }
}

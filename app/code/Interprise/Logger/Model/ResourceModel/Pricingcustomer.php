<?php


namespace Interprise\Logger\Model\ResourceModel;

class Pricingcustomer extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('interprise_pricing_customer', 'id');
    }
}

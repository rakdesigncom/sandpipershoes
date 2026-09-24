<?php


namespace Interprise\Logger\Model\ResourceModel;

class Shippingstoreinterprise extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('interprise_shipping_store_interprise', 'id');
    }
}

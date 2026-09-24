<?php


namespace Interprise\Logger\Model\ResourceModel;

class Pricelists extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('interprise_price_lists', 'id');
    }
}

<?php


namespace Interprise\Logger\Model\ResourceModel;

class Custompaymentitem extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('interprise_custom_payment_item', 'id');
    }
}

<?php

namespace Interprise\Logger\Model\ResourceModel;

class OrderLog extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('interprise_order_log', 'entity_id');
    }
}

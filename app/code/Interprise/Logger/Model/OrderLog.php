<?php

namespace Interprise\Logger\Model;

class OrderLog extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(\Interprise\Logger\Model\ResourceModel\OrderLog::class);
    }
}

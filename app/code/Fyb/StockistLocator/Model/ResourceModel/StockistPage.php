<?php
namespace Fyb\StockistLocator\Model\ResourceModel;

class StockistPage extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('fyb_stockist_page', 'entity_id');
    }
}


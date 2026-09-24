<?php

namespace Fyb\StockistLocator\Model\ResourceModel\StockistPage;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Fyb\StockistLocator\Model\StockistPage', 'Fyb\StockistLocator\Model\ResourceModel\StockistPage'
        );
    }

}


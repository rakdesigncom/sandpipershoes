<?php


namespace Interprise\Logger\Model\ResourceModel;

class Countryclassmapping extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('interprise_country_class_mapping', 'id');
    }
}

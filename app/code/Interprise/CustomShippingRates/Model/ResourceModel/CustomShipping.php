<?php



namespace Interprise\CustomShippingRates\Model\ResourceModel;



class CustomShipping extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb

{
    public function __construct(
		\Magento\Framework\Model\ResourceModel\Db\Context $context
	)
	{
		parent::__construct($context);
	}

    protected function _construct()

    {

        $this->_init('custom_shipping_rates', 'id');

    }

}
<?php

namespace Interprise\CustomShippingRates\Plugin;

class Flatrate
{
    protected $customerSession;
    protected $rateResultFactory;
    protected $rateMethodFactory;

    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
    )
    {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_customerSession = $customerSession;
        $this->_rateMethodFactory = $rateMethodFactory;
    }   

    public function afterCollectRates(\Magento\OfflineShipping\Model\Carrier\Flatrate $subject, $result)
    {
        $result = $this->_rateResultFactory->create();
        $shippingPrice = "4.99"; //custom price

        if (!$this->_customerSession->isLoggedIn()) {
            $method = $this->_rateMethodFactory->create();
            $method->setCarrier('flatrate');
            $method->setCarrierTitle($subject->getConfigData('title'));

            $method->setMethod('flatrate');
            $method->setMethodTitle($subject->getConfigData('name'));

            $method->setPrice($shippingPrice);
            $method->setCost($shippingPrice);
            
            $result->append($method);
        }
        return $result;
    }
}
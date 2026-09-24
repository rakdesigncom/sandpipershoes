<?php

namespace Interprise\Logger\Observer;

use Magento\Framework\Event\ObserverInterface;

class CustomerLogin implements ObserverInterface
{
    protected $_session;

    public function __construct(
        \Magento\Customer\Model\Session $session
    ) {
        $this->_session = $session;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer(); //Get customer object
        $interprise_customer_code = $customer->getData('interprise_customer_code');
        $interprise_pricingmethod = $customer->getData('interprise_pricingmethod');
        $interprise_pricinglevel = $customer->getData('interprise_pricinglevel');
        $interprise_taxcode = $customer->getData('interprise_taxcode');
        $interprise_customertypecode = $customer->getData('interprise_customertypecode');
        $interprise_businesstype = $customer->getData('interprise_businesstype');
        $interprise_discount = $customer->getData('interprise_discount');
        $interprise_ptgroup = $customer->getData('interprise_ptgroup');
        $interprise_ptcode = $customer->getData('interprise_ptcode');
        $interprise_defaultprice = $customer->getData('interprise_defaultprice');

        $customerSession = $this->_session;
        $customerSession->setISCode($interprise_customer_code);
        $customerSession->setISPricinglevel($interprise_pricinglevel);
        $customerSession->setISTaxcode($interprise_taxcode);
        $customerSession->setInterprisepricingmethod($interprise_pricingmethod);
        $customerSession->setInterprisediscount($interprise_discount);
        $customerSession->setInterprisePtGroup($interprise_ptgroup);
        $customerSession->setInterprisePtCode($interprise_ptcode);
        $customerSession->setInterpriseDefaultprice($interprise_defaultprice);
    }
}

<?php

namespace Interprise\CustomShippingRates\Observer;

use Magento\Framework\Event\ObserverInterface;

class SalesOrderSaveAfterObserver implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $ipshippingmethod = $observer->getQuote()->getIsShippingmethod();
        // echo $ipshippingmethod;
        // die;
        $order = $observer->getOrder();
        $order->setIsShippingmethod($ipshippingmethod);
    }
}

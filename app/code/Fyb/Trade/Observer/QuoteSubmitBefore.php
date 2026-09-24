<?php

namespace Fyb\Trade\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class QuoteSubmitBefore implements ObserverInterface
{
    /**
     * Observer for sales_model_service_quote_submit_before
     *
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getOrder();
        $quote = $observer->getQuote();

        if (!$quote->isVirtual()) {
            $order->getShippingAddress()->setIsDropship($quote->getShippingAddress()->getIsDropship());
        }
    }
}

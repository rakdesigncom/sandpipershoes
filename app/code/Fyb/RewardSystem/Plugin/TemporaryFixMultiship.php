<?php

namespace Fyb\RewardSystem\Plugin;

use Magento\Framework\Event\Observer;
use Webkul\RewardSystem\Observer\MultiShipObserver;

class TemporaryFixMultiship
{
    /**
     * @param MultiShipObserver $subject
     * @param callable $proceed
     * @param Observer $observer
     *
     * @return void
     */
    public function aroundExecute(MultiShipObserver $subject, callable $proceed, Observer $observer): void
    {
        return;
        $proceed($observer);
    }
}

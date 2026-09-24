<?php

namespace Fyb\Trade\Plugin;

use Magento\Customer\Block\Address\Edit;

class DisableBillingAddressChange
{
    /**
     * @var \Fyb\Trade\Helper\Data
     */
    protected $tradeHelper;

    /**
     * @param \Fyb\Trade\Helper\Data $tradeHelper
     */
    public function __construct(\Fyb\Trade\Helper\Data $tradeHelper)
    {
        $this->tradeHelper = $tradeHelper;
    }

    /**
     * @param Edit $subject
     * @param callable $proceed
     *
     * @return bool
     */
    public function aroundCanSetAsDefaultBilling(Edit $subject, callable $proceed): bool
    {
        if ($this->tradeHelper->isTradeStore()) {
            return false;
        }

        return $proceed();
    }
}

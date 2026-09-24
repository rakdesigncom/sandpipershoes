<?php

namespace Fyb\RewardSystem\Helper;

class EarnHelper extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
    ) {
        parent::__construct($context);
    }


    public function getPricePointValue($scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            'rewardsystem/general_settings/product_dynamic_reward_qty',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $scopeCode
        );
    }

    public function isDynamicRewardEnabled($scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            'rewardsystem/general_settings/product_dynamic_reward',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $scopeCode
        );
    }

    public function isDynamicCartRewardEnabled($scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            'rewardsystem/general_settings/product_dynamic_reward',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $scopeCode
        );
    }

    public function calculateQuoteItem($quoteItem)
    {
        return $this->calculateItem($quoteItem);
    }

    public function calculateOrderItem($orderItem)
    {
        return $this->calculateItem($orderItem);
    }

    protected function calculateItem($item)
    {
        $rewardQty = $this->getPricePointValue() ?: 10;
        $discount = $item->getBasePrice() * $item->getDiscountPercent() / 100;
        $price = floor(($item->getBasePrice() - $discount) / 10);

        return $rewardQty * round($price);
    }

    public function calculateOnAmount($amount)
    {
        $rewardQty = $this->getPricePointValue() ?: 10;
        $price = floor($amount / 10);

        return $rewardQty * round($price);
    }
}

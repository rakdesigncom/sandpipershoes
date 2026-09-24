<?php

namespace Fyb\RewardSystem\Plugin;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Webkul\RewardSystem\Helper\Data;

class DynamicProductReward
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Webkul\RewardSystem\Model\RewardorderDetailFactory
     */
    protected $_RewardorderDetailFactory;

    /**
     * @var \Fyb\RewardSystem\Helper\EarnHelper
     */
    protected $earnHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    protected $orderCollectionFactory;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Webkul\RewardSystem\Model\RewardorderDetailFactory $_RewardorderDetailFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        \Webkul\RewardSystem\Model\RewardorderDetailFactory $_RewardorderDetailFactory,
        \Fyb\RewardSystem\Helper\EarnHelper $earnHelper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->_RewardorderDetailFactory = $_RewardorderDetailFactory;
        $this->earnHelper = $earnHelper;
        $this->checkoutSession = $checkoutSession;
        $this->orderCollectionFactory = $orderCollectionFactory;
    }

    public function aroundCalculateCreditAmountforOrder(Data $subject, \Closure $proceed, $orderId = 0, $order = null)
    {
        $priority = $subject->getrewardPriority();
        if ($priority == 1 && $this->earnHelper->isDynamicCartRewardEnabled()) {
            /** @var \Magento\Sales\Model\Order $order */
            $order = $this->orderCollectionFactory->create()
                ->addAttributeToSelect('*')
                ->addFieldToFilter('entity_id', $orderId)
                ->getFirstItem();
            if (!$order->getId()) {
                return 0;
            }

            $cartAmount = $order->getBaseSubtotal();
            if ($order->getBaseRewardAmount() < 0) {
                $cartAmount = max($cartAmount - abs($order->getBaseRewardAmount()), 0);
            }

            $reward = $this->earnHelper->calculateOnAmount($cartAmount);

            return $reward;
        }

        return $proceed();
    }

    /**
     * @param Data $subject
     * @param \Closure $proceed
     * @param \Magento\Sales\Model\Order\Item $item
     * @param bool $quantityWise
     *
     * @return float
     */
    public function aroundGetProductData(Data $subject, \Closure $proceed, $item, $quantityWise): float
    {
        if ($this->earnHelper->isDynamicRewardEnabled()) {
            $rewardPoints = $this->earnHelper->calculateOrderItem($item);

            if ($item->getOrderId() && $item->getOrderId() != 0) {
                $qty = $item->getQtyOrdered();
            } else {
                $qty = $item->getQty();
            }

            if ($quantityWise) {
                $rewardPoints *= $qty;
            }

            return $rewardPoints;
        }

        return $proceed($item, $quantityWise);
    }

    public function isDynamicRewardEnabled()
    {
        return $this->scopeConfig->getValue(
            'rewardsystem/general_settings/product_dynamic_reward',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getPricePointValue()
    {
        return $this->scopeConfig->getValue(
            'rewardsystem/general_settings/product_dynamic_reward_qty',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function aroundGetCartReward(Data $subject, \Closure $proceed)
    {
        if ($this->earnHelper->isDynamicCartRewardEnabled()) {
            $quote = $this->checkoutSession->getQuote();
            $cartAmount = $quote->getBaseSubtotal();
            if ($rewardInfo = $quote->getRewardInfo()) {
                $rewardInfo = json_decode($rewardInfo, true);
                if (!empty($rewardInfo['amount'])) {
                    $cartAmount = max($cartAmount - $rewardInfo['amount'], 0);
                }
            }
            $reward = $this->earnHelper->calculateOnAmount($cartAmount);
            $amountFrom = floor($cartAmount / 10) * 10;

            return [
                'reward' => $reward,
                'amount_from' => $amountFrom,
                'amount_to' => $amountFrom + 9.99,
                'amount_rule' => 0,
            ];
        }

        return $proceed();
    }

    /**
     * @param \Webkul\RewardSystem\Helper\Data $subject
     * @param \Closure $proceed
     * @param $order
     *
     * @return float|int|void
     */
    public function aroundUpdateRewardOrderDetailData(Data $subject, \Closure $proceed, $order)
    {
        if ($this->earnHelper->isDynamicRewardEnabled()) {
            $storeId = $order->getStoreId();
            $isQtyWise = $subject->getrewardQuantityWise();

            foreach ($order->getAllVisibleItems() as $_item) {
                $itemRewardPoint = $this->earnHelper->calculateOrderItem($_item);
                if (!$itemRewardPoint) {
                    continue;
                }

                if ($isQtyWise) {
                    $itemRewardPoint *= $_item->getQtyOrdered();
                }

                $rewardOrderDetail = $this->_RewardorderDetailFactory->create();
                $rewardOrderDetail->addData([
                    "order_id" => $order->getId(),
                    "item_id" => $_item->getProductId(),
                    "points" => $itemRewardPoint,
                    "qty" => $_item->getQtyOrdered(),
                    "is_qty_wise" => $isQtyWise,
                ]);
                $rewardOrderDetail->save();
            }
        } else {
            $proceed($order);
        }
    }
}

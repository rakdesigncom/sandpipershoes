<?php

namespace Fyb\RewardSystem\Plugin;

use Webkul\RewardSystem\Helper\Data;

class RewardStoreSet
{
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    ) {
        $this->customerRepository = $customerRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param Data $subject
     * @param string $msg
     * @param string $adminMsg
     * @param array $rewardData
     * @param int $storeId
     *
     * @return array
     */
    public function beforeUpdateRewardRecordData(Data $subject, $msg, $adminMsg, $rewardData, $storeId = 0)
    {

        if (!$storeId) {
            try {
                if (!empty($rewardData['customer_id'])) {
                    $customer = $this->customerRepository->getById($rewardData['customer_id']);
                    $storeId = (int)$customer->getStoreId();
                }
            } catch (\Exception $e) {
            }

            try {
                if (!$storeId && !empty($rewardData['order_id'])) {
                    $order = $this->orderRepository->get($rewardData['order_id']);
                    $storeId = (int)$order->getStoreId();
                }
            } catch (\Exception $e) {
            }
        }

        return [$msg, $adminMsg, $rewardData, $storeId];
    }
}

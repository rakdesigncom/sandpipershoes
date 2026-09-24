<?php

namespace Interprise\Logger\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Exception\LocalizedException;

class Order extends AbstractHelper
{
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Interprise\Logger\Model\ResourceModel\FailedOrders\CollectionFactory
     */
    protected $failedOrdersCollectionFactory;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Interprise\Logger\Model\FailedOrdersFactory $failedOrdersFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Interprise\Logger\Model\ResourceModel\FailedOrders\CollectionFactory $failedOrdersCollectionFactory,
    ) {
        parent::__construct($context);

        $this->orderFactory = $orderFactory;
        $this->failedOrdersCollectionFactory = $failedOrdersCollectionFactory;
    }

    /**
     * @param int $orderId
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getOrderSyncStatus($orderId)
    {
        $status = [
            'error' => '',
            'id' => '',
        ];

        $order = $this->orderFactory->create()->load($orderId);
        if (!$order->getId()) {
            throw new LocalizedException(__('Order not found.'));
        }

        if ($order->getData('so_number')) {
            $status['id'] = $order->getData('so_number');
        } else {
            $failedOrder = $this->getFailedOrder($orderId);
            if ($failedOrder->getId()) {
                $status['error'] = json_decode($failedOrder->getData('Reason')) ?: 'Error to Sync';
            }
        }

        return $status;
    }

    /**
     * @param int $orderId
     *
     * @return \Interprise\Logger\Model\FailedOrders
     */
    public function getFailedOrder($orderId)
    {
        $collection = $this->failedOrdersCollectionFactory->create()
            ->addFieldToFilter('Changelog_item_id', ['eq' => $orderId])
            ->setOrder('failedorder_id', 'DESC')
            ->setPageSize(1)->setCurPage(1);

        return $collection->getFirstItem();
    }
}

<?php

namespace Interprise\Logger\Model\Order;

use Magento\Framework\Exception\LocalizedException;

class SyncQueue
{
    /**
     * @var \Interprise\Logger\Model\ResourceModel\Changelog\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Interprise\Logger\Model\ChangelogFactory
     */
    private $changeLogFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;

    /**
     * @param \Interprise\Logger\Model\ResourceModel\Changelog\CollectionFactory $collectionFactory
     * @param \Interprise\Logger\Model\ChangelogFactory $changeLogFactory
     * @param \Interprise\Logger\Helper\Data $helper
     */
    public function __construct(
        \Interprise\Logger\Model\ResourceModel\Changelog\CollectionFactory $collectionFactory,
        \Interprise\Logger\Model\ChangelogFactory $changeLogFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->changeLogFactory = $changeLogFactory;
        $this->dateTime = $dateTime;
    }

    /**
     * Adds order to queue
     *
     * @param int $orderId
     *
     * @return \Interprise\Logger\Model\Changelog
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function add($orderId)
    {
        if (!$orderId) {
            throw new LocalizedException(__('Order not found.'));
        }

        if ($this->isAlreadyInQueue($orderId)) {
            throw new LocalizedException(__('Order already in queue.'));
        }

        $changeLog = $this->changeLogFactory->create();
        $changeLog->addData([
            'CreatedAt' => $this->dateTime->gmtDate(),
            'ItemType' => 'order',
            'ItemId' => $orderId,
            'Action' => 'POST',
            'PushedStatus' => 0,
        ]);

        $changeLog->save();

        return $changeLog;
    }

    /**
     * @param int $orderId
     *
     * @return bool
     */
    protected function isAlreadyInQueue($orderId): bool
    {
        $collection = $this->collectionFactory->create()->addFieldToFilter('ItemId', $orderId)
            ->addFieldToFilter('ItemType', 'order')
            ->addFieldToFilter('PushedStatus', 0);

        return (bool)$collection->getFirstItem()->getId();
    }
}

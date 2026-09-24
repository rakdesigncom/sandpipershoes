<?php


namespace Interprise\Logger\Api\Data;

interface FailedOrdersSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get CronActivitySchedule list.
     * @return \Interprise\Logger\Api\Data\FailedOrdersInterface[]
     */
    public function getItems();

    /**
     * Set CronLogId list.
     * @param \Interprise\Logger\Api\Data\FailedOrdersInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

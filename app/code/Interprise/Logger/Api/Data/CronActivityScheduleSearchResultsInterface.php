<?php


namespace Interprise\Logger\Api\Data;

interface CronActivityScheduleSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get CronActivitySchedule list.
     * @return \Interprise\Logger\Api\Data\CronActivityScheduleInterface[]
     */
    public function getItems();

    /**
     * Set CronLogId list.
     * @param \Interprise\Logger\Api\Data\CronActivityScheduleInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

<?php


namespace Interprise\Logger\Api\Data;

interface CronLogSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get CronLog list.
     * @return \Interprise\Logger\Api\Data\CronLogInterface[]
     */
    public function getItems();

    /**
     * Set CronMasterId list.
     * @param \Interprise\Logger\Api\Data\CronLogInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

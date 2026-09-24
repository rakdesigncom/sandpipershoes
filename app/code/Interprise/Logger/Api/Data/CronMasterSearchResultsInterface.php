<?php


namespace Interprise\Logger\Api\Data;

interface CronMasterSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get CronMaster list.
     * @return \Interprise\Logger\Api\Data\CronMasterInterface[]
     */
    public function getItems();

    /**
     * Set cron_name list.
     * @param \Interprise\Logger\Api\Data\CronMasterInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

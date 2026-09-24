<?php


namespace Interprise\Logger\Api\Data;

interface ChangelogSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get Changelog list.
     * @return \Interprise\Logger\Api\Data\ChangelogInterface[]
     */
    public function getItems();

    /**
     * Set CreatedAt list.
     * @param \Interprise\Logger\Api\Data\ChangelogInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

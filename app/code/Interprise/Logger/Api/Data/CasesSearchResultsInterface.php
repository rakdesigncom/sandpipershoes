<?php


namespace Interprise\Logger\Api\Data;

interface CasesSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get case list.
     * @return \Interprise\Logger\Api\Data\CasesInterface[]
     */
    public function getItems();

    /**
     * Set store_id list.
     * @param \Interprise\Logger\Api\Data\CasesInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

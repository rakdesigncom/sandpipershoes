<?php


namespace Interprise\Logger\Api\Data;

interface TransactionmasterSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get transactionmaster list.
     * @return \Interprise\Logger\Api\Data\TransactionmasterInterface[]
     */
    public function getItems();

    /**
     * Set customer_id list.
     * @param \Interprise\Logger\Api\Data\TransactionmasterInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

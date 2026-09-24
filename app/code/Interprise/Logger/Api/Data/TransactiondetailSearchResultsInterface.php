<?php


namespace Interprise\Logger\Api\Data;

interface TransactiondetailSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get transactiondetail list.
     * @return \Interprise\Logger\Api\Data\TransactiondetailInterface[]
     */
    public function getItems();

    /**
     * Set customer_id list.
     * @param \Interprise\Logger\Api\Data\TransactiondetailInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

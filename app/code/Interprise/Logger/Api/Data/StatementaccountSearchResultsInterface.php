<?php


namespace Interprise\Logger\Api\Data;

interface StatementaccountSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get statementaccount list.
     * @return \Interprise\Logger\Api\Data\StatementaccountInterface[]
     */
    public function getItems();

    /**
     * Set customer_id list.
     * @param \Interprise\Logger\Api\Data\StatementaccountInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

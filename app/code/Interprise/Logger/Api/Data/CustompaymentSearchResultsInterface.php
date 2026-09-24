<?php


namespace Interprise\Logger\Api\Data;

interface CustompaymentSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get custompayment list.
     * @return \Interprise\Logger\Api\Data\CustompaymentInterface[]
     */
    public function getItems();

    /**
     * Set customer_id list.
     * @param \Interprise\Logger\Api\Data\CustompaymentInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

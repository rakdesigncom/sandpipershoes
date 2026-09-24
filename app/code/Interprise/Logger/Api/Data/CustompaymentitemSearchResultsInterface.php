<?php


namespace Interprise\Logger\Api\Data;

interface CustompaymentitemSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get custompaymentitem list.
     * @return \Interprise\Logger\Api\Data\CustompaymentitemInterface[]
     */
    public function getItems();

    /**
     * Set payment_id list.
     * @param \Interprise\Logger\Api\Data\CustompaymentitemInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

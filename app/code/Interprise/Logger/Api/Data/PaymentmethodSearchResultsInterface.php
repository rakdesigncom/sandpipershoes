<?php


namespace Interprise\Logger\Api\Data;

interface PaymentmethodSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get paymentmethod list.
     * @return \Interprise\Logger\Api\Data\PaymentmethodInterface[]
     */
    public function getItems();

    /**
     * Set is_payment_term_group list.
     * @param \Interprise\Logger\Api\Data\PaymentmethodInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

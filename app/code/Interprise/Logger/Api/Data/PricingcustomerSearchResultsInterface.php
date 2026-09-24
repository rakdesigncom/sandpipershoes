<?php


namespace Interprise\Logger\Api\Data;

interface PricingcustomerSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get pricingcustomer list.
     * @return \Interprise\Logger\Api\Data\PricingcustomerInterface[]
     */
    public function getItems();

    /**
     * Set item_code list.
     * @param \Interprise\Logger\Api\Data\PricingcustomerInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

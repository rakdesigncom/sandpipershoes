<?php


namespace Interprise\Logger\Api\Data;

interface ShippingstoreinterpriseSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get shippingstoreinterprise list.
     * @return \Interprise\Logger\Api\Data\ShippingstoreinterpriseInterface[]
     */
    public function getItems();

    /**
     * Set store_id list.
     * @param \Interprise\Logger\Api\Data\ShippingstoreinterpriseInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

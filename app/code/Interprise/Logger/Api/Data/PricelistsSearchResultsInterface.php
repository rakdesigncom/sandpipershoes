<?php


namespace Interprise\Logger\Api\Data;

interface PricelistsSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get pricelists list.
     * @return \Interprise\Logger\Api\Data\PricelistsInterface[]
     */
    public function getItems();

    /**
     * Set itemcode list.
     * @param \Interprise\Logger\Api\Data\PricelistsInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

<?php


namespace Interprise\Logger\Api\Data;

interface CountryclassmappingSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get countryclassmapping list.
     * @return \Interprise\Logger\Api\Data\CountryclassmappingInterface[]
     */
    public function getItems();

    /**
     * Set iso_code list.
     * @param \Interprise\Logger\Api\Data\CountryclassmappingInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

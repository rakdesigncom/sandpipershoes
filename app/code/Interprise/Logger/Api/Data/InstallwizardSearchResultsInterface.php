<?php


namespace Interprise\Logger\Api\Data;

interface InstallwizardSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get installwizard list.
     * @return \Interprise\Logger\Api\Data\InstallwizardInterface[]
     */
    public function getItems();

    /**
     * Set item_name list.
     * @param \Interprise\Logger\Api\Data\InstallwizardInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

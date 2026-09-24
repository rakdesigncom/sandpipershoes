<?php


namespace Interprise\Logger\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface PricelistsRepositoryInterface
{

    /**
     * Save pricelists
     * @param \Interprise\Logger\Api\Data\PricelistsInterface $pricelists
     * @return \Interprise\Logger\Api\Data\PricelistsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Interprise\Logger\Api\Data\PricelistsInterface $pricelists
    );

    /**
     * Retrieve pricelists
     * @param string $pricelistsId
     * @return \Interprise\Logger\Api\Data\PricelistsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($pricelistsId);

    /**
     * Retrieve pricelists matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Interprise\Logger\Api\Data\PricelistsSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete pricelists
     * @param \Interprise\Logger\Api\Data\PricelistsInterface $pricelists
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Interprise\Logger\Api\Data\PricelistsInterface $pricelists
    );

    /**
     * Delete pricelists by ID
     * @param string $pricelistsId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($pricelistsId);
}

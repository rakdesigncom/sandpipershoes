<?php


namespace Interprise\Logger\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface FailedOrdersRepositoryInterface
{
    /**
     * Save FailedOrders
     * @param \Interprise\Logger\Api\Data\FailedOrdersInterface $cronLog
     * @return \Interprise\Logger\Api\Data\FailedOrdersInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Interprise\Logger\Api\Data\FailedOrdersInterface $cronLog
    );

    /**
     * Retrieve FailedOrders
     * @param string $failedordersId
     * @return \Interprise\Logger\Api\Data\FailedOrdersInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($failedordersId);

    /**
     * Retrieve FailedOrders matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Interprise\Logger\Api\Data\FailedOrdersSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete FailedOrders
     * @param \Interprise\Logger\Api\Data\FailedOrdersInterface $cronLog
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Interprise\Logger\Api\Data\FailedOrdersInterface $cronLog
    );

    /**
     * Delete FailedOrders by ID
     * @param string $failedordersId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($failedordersId);
}

<?php


namespace Interprise\Logger\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface CronLogRepositoryInterface
{
    /**
     * Save CronLog
     * @param \Interprise\Logger\Api\Data\CronLogInterface $cronLog
     * @return \Interprise\Logger\Api\Data\CronLogInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Interprise\Logger\Api\Data\CronLogInterface $cronLog
    );

    /**
     * Retrieve CronLog
     * @param string $cronlogId
     * @return \Interprise\Logger\Api\Data\CronLogInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($cronlogId);

    /**
     * Retrieve CronLog matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Interprise\Logger\Api\Data\CronLogSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete CronLog
     * @param \Interprise\Logger\Api\Data\CronLogInterface $cronLog
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Interprise\Logger\Api\Data\CronLogInterface $cronLog
    );

    /**
     * Delete CronLog by ID
     * @param string $cronlogId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($cronlogId);
}

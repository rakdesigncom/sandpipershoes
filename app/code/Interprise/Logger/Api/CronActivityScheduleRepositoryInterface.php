<?php


namespace Interprise\Logger\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface CronActivityScheduleRepositoryInterface
{
    /**
     * Save CronActivitySchedule
     * @param \Interprise\Logger\Api\Data\CronActivityScheduleInterface $cronActivitySchedule
     * @return \Interprise\Logger\Api\Data\CronActivityScheduleInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Interprise\Logger\Api\Data\CronActivityScheduleInterface $cronActivitySchedule
    );

    /**
     * Retrieve CronActivitySchedule
     * @param string $cronactivityscheduleId
     * @return \Interprise\Logger\Api\Data\CronActivityScheduleInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($cronactivityscheduleId);

    /**
     * Retrieve CronActivitySchedule matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Interprise\Logger\Api\Data\CronActivityScheduleSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete CronActivitySchedule
     * @param \Interprise\Logger\Api\Data\CronActivityScheduleInterface $cronActivitySchedule
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Interprise\Logger\Api\Data\CronActivityScheduleInterface $cronActivitySchedule
    );

    /**
     * Delete CronActivitySchedule by ID
     * @param string $cronactivityscheduleId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($cronactivityscheduleId);
}

<?php


namespace Interprise\Logger\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface CronMasterRepositoryInterface
{
    /**
     * Save CronMaster
     * @param \Interprise\Logger\Api\Data\CronMasterInterface $cronMaster
     * @return \Interprise\Logger\Api\Data\CronMasterInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Interprise\Logger\Api\Data\CronMasterInterface $cronMaster
    );

    /**
     * Retrieve CronMaster
     * @param string $cronmasterId
     * @return \Interprise\Logger\Api\Data\CronMasterInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($cronmasterId);

    /**
     * Retrieve CronMaster matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Interprise\Logger\Api\Data\CronMasterSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete CronMaster
     * @param \Interprise\Logger\Api\Data\CronMasterInterface $cronMaster
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Interprise\Logger\Api\Data\CronMasterInterface $cronMaster
    );

    /**
     * Delete CronMaster by ID
     * @param string $cronmasterId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($cronmasterId);
}

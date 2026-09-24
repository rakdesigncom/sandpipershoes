<?php


namespace Interprise\Logger\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ReattemptFrequencyRepositoryInterface
{
    /**
     * Save ReattemptFrequency
     * @param \Interprise\Logger\Api\Data\ReattemptFrequencyInterface $cronLog
     * @return \Interprise\Logger\Api\Data\ReattemptFrequencyInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Interprise\Logger\Api\Data\ReattemptFrequencyInterface $cronLog
    );

    /**
     * Retrieve ReattemptFrequency
     * @param string $failedordersId
     * @return \Interprise\Logger\Api\Data\ReattemptFrequencyInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($failedordersId);

    /**
     * Retrieve ReattemptFrequency matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Interprise\Logger\Api\Data\ReattemptFrequencySearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete ReattemptFrequency
     * @param \Interprise\Logger\Api\Data\ReattemptFrequencyInterface $cronLog
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Interprise\Logger\Api\Data\ReattemptFrequencyInterface $cronLog
    );

    /**
     * Delete ReattemptFrequency by ID
     * @param string $failedordersId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($failedordersId);
}

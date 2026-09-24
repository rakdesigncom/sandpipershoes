<?php


namespace Interprise\Logger\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface CasesRepositoryInterface
{

    /**
     * Save case
     * @param \Interprise\Logger\Api\Data\CasesInterface $case
     * @return \Interprise\Logger\Api\Data\CasesInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Interprise\Logger\Api\Data\CasesInterface $case
    );

    /**
     * Retrieve case
     * @param string $caseId
     * @return \Interprise\Logger\Api\Data\CasesInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($caseId);

    /**
     * Retrieve case matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Interprise\Logger\Api\Data\CasesSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete case
     * @param \Interprise\Logger\Api\Data\CasesInterface $case
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Interprise\Logger\Api\Data\CasesInterface $case
    );

    /**
     * Delete case by ID
     * @param string $caseId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($caseId);
}

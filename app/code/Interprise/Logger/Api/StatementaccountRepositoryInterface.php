<?php


namespace Interprise\Logger\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface StatementaccountRepositoryInterface
{

    /**
     * Save statementaccount
     * @param \Interprise\Logger\Api\Data\StatementaccountInterface $statementaccount
     * @return \Interprise\Logger\Api\Data\StatementaccountInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Interprise\Logger\Api\Data\StatementaccountInterface $statementaccount
    );

    /**
     * Retrieve statementaccount
     * @param string $statementaccountId
     * @return \Interprise\Logger\Api\Data\StatementaccountInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($statementaccountId);

    /**
     * Retrieve statementaccount matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Interprise\Logger\Api\Data\StatementaccountSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete statementaccount
     * @param \Interprise\Logger\Api\Data\StatementaccountInterface $statementaccount
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Interprise\Logger\Api\Data\StatementaccountInterface $statementaccount
    );

    /**
     * Delete statementaccount by ID
     * @param string $statementaccountId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($statementaccountId);
}

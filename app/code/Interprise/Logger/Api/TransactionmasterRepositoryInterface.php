<?php


namespace Interprise\Logger\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface TransactionmasterRepositoryInterface
{

    /**
     * Save transactionmaster
     * @param \Interprise\Logger\Api\Data\TransactionmasterInterface $transactionmaster
     * @return \Interprise\Logger\Api\Data\TransactionmasterInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Interprise\Logger\Api\Data\TransactionmasterInterface $transactionmaster
    );

    /**
     * Retrieve transactionmaster
     * @param string $transactionmasterId
     * @return \Interprise\Logger\Api\Data\TransactionmasterInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($transactionmasterId);

    /**
     * Retrieve transactionmaster matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Interprise\Logger\Api\Data\TransactionmasterSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete transactionmaster
     * @param \Interprise\Logger\Api\Data\TransactionmasterInterface $transactionmaster
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Interprise\Logger\Api\Data\TransactionmasterInterface $transactionmaster
    );

    /**
     * Delete transactionmaster by ID
     * @param string $transactionmasterId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($transactionmasterId);
}

<?php


namespace Interprise\Logger\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface TransactiondetailRepositoryInterface
{

    /**
     * Save transactiondetail
     * @param \Interprise\Logger\Api\Data\TransactiondetailInterface $transactiondetail
     * @return \Interprise\Logger\Api\Data\TransactiondetailInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Interprise\Logger\Api\Data\TransactiondetailInterface $transactiondetail
    );

    /**
     * Retrieve transactiondetail
     * @param string $transactiondetailId
     * @return \Interprise\Logger\Api\Data\TransactiondetailInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($transactiondetailId);

    /**
     * Retrieve transactiondetail matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Interprise\Logger\Api\Data\TransactiondetailSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete transactiondetail
     * @param \Interprise\Logger\Api\Data\TransactiondetailInterface $transactiondetail
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Interprise\Logger\Api\Data\TransactiondetailInterface $transactiondetail
    );

    /**
     * Delete transactiondetail by ID
     * @param string $transactiondetailId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($transactiondetailId);
}

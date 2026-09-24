<?php


namespace Interprise\Logger\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface CustompaymentRepositoryInterface
{

    /**
     * Save custompayment
     * @param \Interprise\Logger\Api\Data\CustompaymentInterface $custompayment
     * @return \Interprise\Logger\Api\Data\CustompaymentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Interprise\Logger\Api\Data\CustompaymentInterface $custompayment
    );

    /**
     * Retrieve custompayment
     * @param string $custompaymentId
     * @return \Interprise\Logger\Api\Data\CustompaymentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($custompaymentId);

    /**
     * Retrieve custompayment matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Interprise\Logger\Api\Data\CustompaymentSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete custompayment
     * @param \Interprise\Logger\Api\Data\CustompaymentInterface $custompayment
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Interprise\Logger\Api\Data\CustompaymentInterface $custompayment
    );

    /**
     * Delete custompayment by ID
     * @param string $custompaymentId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($custompaymentId);
}

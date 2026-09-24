<?php


namespace Interprise\Logger\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface CustompaymentitemRepositoryInterface
{

    /**
     * Save custompaymentitem
     * @param \Interprise\Logger\Api\Data\CustompaymentitemInterface $custompaymentitem
     * @return \Interprise\Logger\Api\Data\CustompaymentitemInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Interprise\Logger\Api\Data\CustompaymentitemInterface $custompaymentitem
    );

    /**
     * Retrieve custompaymentitem
     * @param string $custompaymentitemId
     * @return \Interprise\Logger\Api\Data\CustompaymentitemInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($custompaymentitemId);

    /**
     * Retrieve custompaymentitem matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Interprise\Logger\Api\Data\CustompaymentitemSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete custompaymentitem
     * @param \Interprise\Logger\Api\Data\CustompaymentitemInterface $custompaymentitem
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Interprise\Logger\Api\Data\CustompaymentitemInterface $custompaymentitem
    );

    /**
     * Delete custompaymentitem by ID
     * @param string $custompaymentitemId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($custompaymentitemId);
}

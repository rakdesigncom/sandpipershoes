<?php


namespace Interprise\Logger\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface PaymentmethodRepositoryInterface
{

    /**
     * Save paymentmethod
     * @param \Interprise\Logger\Api\Data\PaymentmethodInterface $paymentmethod
     * @return \Interprise\Logger\Api\Data\PaymentmethodInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Interprise\Logger\Api\Data\PaymentmethodInterface $paymentmethod
    );

    /**
     * Retrieve paymentmethod
     * @param string $paymentmethodId
     * @return \Interprise\Logger\Api\Data\PaymentmethodInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($paymentmethodId);

    /**
     * Retrieve paymentmethod matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Interprise\Logger\Api\Data\PaymentmethodSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete paymentmethod
     * @param \Interprise\Logger\Api\Data\PaymentmethodInterface $paymentmethod
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Interprise\Logger\Api\Data\PaymentmethodInterface $paymentmethod
    );

    /**
     * Delete paymentmethod by ID
     * @param string $paymentmethodId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($paymentmethodId);
}

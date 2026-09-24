<?php


namespace Interprise\Logger\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface PricingcustomerRepositoryInterface
{

    /**
     * Save pricingcustomer
     * @param \Interprise\Logger\Api\Data\PricingcustomerInterface $pricingcustomer
     * @return \Interprise\Logger\Api\Data\PricingcustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Interprise\Logger\Api\Data\PricingcustomerInterface $pricingcustomer
    );

    /**
     * Retrieve pricingcustomer
     * @param string $pricingcustomerId
     * @return \Interprise\Logger\Api\Data\PricingcustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($pricingcustomerId);

    /**
     * Retrieve pricingcustomer matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Interprise\Logger\Api\Data\PricingcustomerSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete pricingcustomer
     * @param \Interprise\Logger\Api\Data\PricingcustomerInterface $pricingcustomer
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Interprise\Logger\Api\Data\PricingcustomerInterface $pricingcustomer
    );

    /**
     * Delete pricingcustomer by ID
     * @param string $pricingcustomerId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($pricingcustomerId);
}

<?php


namespace Interprise\Logger\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ShippingstoreinterpriseRepositoryInterface
{

    /**
     * Save shippingstoreinterprise
     * @param \Interprise\Logger\Api\Data\ShippingstoreinterpriseInterface $shippingstoreinterprise
     * @return \Interprise\Logger\Api\Data\ShippingstoreinterpriseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Interprise\Logger\Api\Data\ShippingstoreinterpriseInterface $shippingstoreinterprise
    );

    /**
     * Retrieve shippingstoreinterprise
     * @param string $shippingstoreinterpriseId
     * @return \Interprise\Logger\Api\Data\ShippingstoreinterpriseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($shippingstoreinterpriseId);

    /**
     * Retrieve shippingstoreinterprise matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Interprise\Logger\Api\Data\ShippingstoreinterpriseSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete shippingstoreinterprise
     * @param \Interprise\Logger\Api\Data\ShippingstoreinterpriseInterface $shippingstoreinterprise
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Interprise\Logger\Api\Data\ShippingstoreinterpriseInterface $shippingstoreinterprise
    );

    /**
     * Delete shippingstoreinterprise by ID
     * @param string $shippingstoreinterpriseId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($shippingstoreinterpriseId);
}

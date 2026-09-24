<?php


namespace Interprise\Logger\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface InstallwizardRepositoryInterface
{

    /**
     * Save installwizard
     * @param \Interprise\Logger\Api\Data\InstallwizardInterface $installwizard
     * @return \Interprise\Logger\Api\Data\InstallwizardInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Interprise\Logger\Api\Data\InstallwizardInterface $installwizard
    );

    /**
     * Retrieve installwizard
     * @param string $installwizardId
     * @return \Interprise\Logger\Api\Data\InstallwizardInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($installwizardId);

    /**
     * Retrieve installwizard matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Interprise\Logger\Api\Data\InstallwizardSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete installwizard
     * @param \Interprise\Logger\Api\Data\InstallwizardInterface $installwizard
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Interprise\Logger\Api\Data\InstallwizardInterface $installwizard
    );

    /**
     * Delete installwizard by ID
     * @param string $installwizardId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($installwizardId);
}

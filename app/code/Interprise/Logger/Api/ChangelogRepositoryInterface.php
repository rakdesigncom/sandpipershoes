<?php


namespace Interprise\Logger\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ChangelogRepositoryInterface
{
    /**
     * Save Changelog
     * @param \Interprise\Logger\Api\Data\ChangelogInterface $changelog
     * @return \Interprise\Logger\Api\Data\ChangelogInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Interprise\Logger\Api\Data\ChangelogInterface $changelog
    );

    /**
     * Retrieve Changelog
     * @param string $changelogId
     * @return \Interprise\Logger\Api\Data\ChangelogInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($changelogId);

    /**
     * Retrieve Changelog matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Interprise\Logger\Api\Data\ChangelogSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Changelog
     * @param \Interprise\Logger\Api\Data\ChangelogInterface $changelog
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Interprise\Logger\Api\Data\ChangelogInterface $changelog
    );

    /**
     * Delete Changelog by ID
     * @param string $changelogId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($changelogId);
}

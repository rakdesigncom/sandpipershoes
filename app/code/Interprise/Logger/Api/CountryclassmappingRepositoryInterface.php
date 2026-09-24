<?php


namespace Interprise\Logger\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface CountryclassmappingRepositoryInterface
{

    /**
     * Save countryclassmapping
     * @param \Interprise\Logger\Api\Data\CountryclassmappingInterface $countryclassmapping
     * @return \Interprise\Logger\Api\Data\CountryclassmappingInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Interprise\Logger\Api\Data\CountryclassmappingInterface $countryclassmapping
    );

    /**
     * Retrieve countryclassmapping
     * @param string $countryclassmappingId
     * @return \Interprise\Logger\Api\Data\CountryclassmappingInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($countryclassmappingId);

    /**
     * Retrieve countryclassmapping matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Interprise\Logger\Api\Data\CountryclassmappingSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete countryclassmapping
     * @param \Interprise\Logger\Api\Data\CountryclassmappingInterface $countryclassmapping
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Interprise\Logger\Api\Data\CountryclassmappingInterface $countryclassmapping
    );

    /**
     * Delete countryclassmapping by ID
     * @param string $countryclassmappingId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($countryclassmappingId);
}

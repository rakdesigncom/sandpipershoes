<?php


namespace Interprise\Logger\Model;

use Magento\Framework\Api\SortOrder;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Exception\NoSuchEntityException;
use Interprise\Logger\Model\ResourceModel\Changelog\CollectionFactory as ChangelogCollectionFactory;
use Interprise\Logger\Model\ResourceModel\Changelog as ResourceChangelog;
use Interprise\Logger\Api\Data\ChangelogInterfaceFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Interprise\Logger\Api\ChangelogRepositoryInterface;
use Interprise\Logger\Api\Data\ChangelogSearchResultsInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Store\Model\StoreManagerInterface;

class ChangelogRepository implements ChangelogRepositoryInterface
{

    protected $changelogCollectionFactory;

    protected $resource;

    protected $changelogFactory;

    protected $dataObjectHelper;

    protected $dataChangelogFactory;

    private $storeManager;

    protected $searchResultsFactory;

    protected $dataObjectProcessor;
    /**
     * @param ResourceChangelog $resource
     * @param ChangelogFactory $changelogFactory
     * @param ChangelogInterfaceFactory $dataChangelogFactory
     * @param ChangelogCollectionFactory $changelogCollectionFactory
     * @param ChangelogSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceChangelog $resource,
        ChangelogFactory $changelogFactory,
        ChangelogInterfaceFactory $dataChangelogFactory,
        ChangelogCollectionFactory $changelogCollectionFactory,
        ChangelogSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->changelogFactory = $changelogFactory;
        $this->changelogCollectionFactory = $changelogCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataChangelogFactory = $dataChangelogFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Interprise\Logger\Api\Data\ChangelogInterface $changelog
    ) {
        /* if (empty($changelog->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $changelog->setStoreId($storeId);
        } */
        try {
            $this->resource->save($changelog);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the changelog: %1',
                $exception->getMessage()
            ));
        }
        return $changelog;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($changelogId)
    {
        $changelog = $this->changelogFactory->create();
        $this->resource->load($changelog, $changelogId);
        if (!$changelog->getId()) {
            throw new NoSuchEntityException(__('Changelog with id "%1" does not exist.', $changelogId));
        }
        return $changelog;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->changelogCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $fields[] = $filter->getField();
                $condition = $filter->getConditionType() ?: 'eq';
                $conditions[] = [$condition => $filter->getValue()];
            }
            $collection->addFieldToFilter($fields, $conditions);
        }
        
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Interprise\Logger\Api\Data\ChangelogInterface $changelog
    ) {
        try {
            $this->resource->delete($changelog);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Changelog: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($changelogId)
    {
        return $this->delete($this->getById($changelogId));
    }
}

<?php


namespace Interprise\Logger\Model;

use Interprise\Logger\Api\Data\FailedOrdersSearchResultsInterfaceFactory;
use Interprise\Logger\Model\ResourceModel\FailedOrders as ResourceFailedOrders;
use Interprise\Logger\Api\FailedOrdersRepositoryInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Reflection\DataObjectProcessor;
use Interprise\Logger\Model\ResourceModel\FailedOrders\CollectionFactory as CronScheduleFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Interprise\Logger\Api\Data\FailedOrdersInterfaceFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Store\Model\StoreManagerInterface;

class FailedOrdersRepository implements FailedOrdersRepositoryInterface
{

    protected $resource;

    protected $dataObjectHelper;

    private $storeManager;

    protected $failedOrdersFactory;

    protected $failedOrdersCollectionFactory;

    protected $searchResultsFactory;

    protected $dataFailedOrdersFactory;

    protected $dataObjectProcessor;
    /**
     * @param ResourceFailedOrders $resource
     * @param FailedOrdersFactory $failedOrdersFactory
     * @param FailedOrdersInterfaceFactory $dataFailedOrdersFactory
     * @param CronScheduleFactory $failedOrdersCollectionFactory
     * @param FailedOrdersSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceFailedOrders $resource,
        FailedOrdersFactory $failedOrdersFactory,
        FailedOrdersInterfaceFactory $dataFailedOrdersFactory,
        CronScheduleFactory $failedOrdersCollectionFactory,
        FailedOrdersSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->failedOrdersFactory = $failedOrdersFactory;
        $this->failedOrdersCollectionFactory = $failedOrdersCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataFailedOrdersFactory = $dataFailedOrdersFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Interprise\Logger\Api\Data\FailedOrdersInterface $failedOrders
    ) {
        /* if (empty($failedOrders->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $failedOrders->setStoreId($storeId);
        } */
        try {
            $this->resource->save($failedOrders);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the failedOrders: %1',
                $exception->getMessage()
            ));
        }
        return $failedOrders;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($failedOrdersId)
    {
        $failedOrders = $this->failedOrdersFactory->create();
        $this->resource->load($failedOrders, $failedOrdersId);
        if (!$failedOrders->getId()) {
            throw new NoSuchEntityException(__('FailedOrders with id "%1" does not exist.', $failedOrdersId));
        }
        return $failedOrders;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->failedOrdersCollectionFactory->create();
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
        \Interprise\Logger\Api\Data\FailedOrdersInterface $failedOrders
    ) {
        try {
            $this->resource->delete($failedOrders);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the FailedOrders: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($failedOrdersId)
    {
        return $this->delete($this->getById($failedOrdersId));
    }
}

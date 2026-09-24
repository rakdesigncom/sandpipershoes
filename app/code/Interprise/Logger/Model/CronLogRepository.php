<?php


namespace Interprise\Logger\Model;

use Magento\Framework\Api\SortOrder;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Exception\NoSuchEntityException;
use Interprise\Logger\Model\ResourceModel\CronLog as ResourceCronLog;
use Interprise\Logger\Api\Data\CronLogSearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Interprise\Logger\Model\ResourceModel\CronLog\CollectionFactory as CronLogCollectionFactory;
use Interprise\Logger\Api\Data\CronLogInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Store\Model\StoreManagerInterface;
use Interprise\Logger\Api\CronLogRepositoryInterface;

class CronLogRepository implements CronLogRepositoryInterface
{

    protected $resource;

    protected $dataObjectHelper;

    protected $dataCronLogFactory;

    private $storeManager;

    protected $cronLogFactory;

    protected $cronLogCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectProcessor;
    /**
     * @param ResourceCronLog $resource
     * @param CronLogFactory $cronLogFactory
     * @param CronLogInterfaceFactory $dataCronLogFactory
     * @param CronLogCollectionFactory $cronLogCollectionFactory
     * @param CronLogSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceCronLog $resource,
        CronLogFactory $cronLogFactory,
        CronLogInterfaceFactory $dataCronLogFactory,
        CronLogCollectionFactory $cronLogCollectionFactory,
        CronLogSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->cronLogFactory = $cronLogFactory;
        $this->cronLogCollectionFactory = $cronLogCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataCronLogFactory = $dataCronLogFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Interprise\Logger\Api\Data\CronLogInterface $cronLog
    ) {
        /* if (empty($cronLog->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $cronLog->setStoreId($storeId);
        } */
        try {
            $this->resource->save($cronLog);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the cronLog: %1',
                $exception->getMessage()
            ));
        }
        return $cronLog;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($cronLogId)
    {
        $cronLog = $this->cronLogFactory->create();
        $this->resource->load($cronLog, $cronLogId);
        if (!$cronLog->getId()) {
            throw new NoSuchEntityException(__('CronLog with id "%1" does not exist.', $cronLogId));
        }
        return $cronLog;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->cronLogCollectionFactory->create();
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
        \Interprise\Logger\Api\Data\CronLogInterface $cronLog
    ) {
        try {
            $this->resource->delete($cronLog);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the CronLog: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($cronLogId)
    {
        return $this->delete($this->getById($cronLogId));
    }
}

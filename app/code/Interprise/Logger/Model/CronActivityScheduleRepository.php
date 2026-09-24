<?php


namespace Interprise\Logger\Model;

use Interprise\Logger\Api\Data\CronActivityScheduleSearchResultsInterfaceFactory;
use Interprise\Logger\Model\ResourceModel\CronActivitySchedule as ResourceCronActivitySchedule;
use Interprise\Logger\Api\CronActivityScheduleRepositoryInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Reflection\DataObjectProcessor;
use Interprise\Logger\Model\ResourceModel\CronActivitySchedule\CollectionFactory as CronScheduleFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Interprise\Logger\Api\Data\CronActivityScheduleInterfaceFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Store\Model\StoreManagerInterface;

class CronActivityScheduleRepository implements CronActivityScheduleRepositoryInterface
{

    protected $resource;

    protected $dataObjectHelper;

    private $storeManager;

    protected $cronActivityScheduleFactory;

    protected $cronActivityScheduleCollectionFactory;

    protected $searchResultsFactory;

    protected $dataCronActivityScheduleFactory;

    protected $dataObjectProcessor;
    /**
     * @param ResourceCronActivitySchedule $resource
     * @param CronActivityScheduleFactory $cronActivityScheduleFactory
     * @param CronActivityScheduleInterfaceFactory $dataCronActivityScheduleFactory
     * @param CronScheduleFactory $cronActivityScheduleCollectionFactory
     * @param CronActivityScheduleSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceCronActivitySchedule $resource,
        CronActivityScheduleFactory $cronActivityScheduleFactory,
        CronActivityScheduleInterfaceFactory $dataCronActivityScheduleFactory,
        CronScheduleFactory $cronActivityScheduleCollectionFactory,
        CronActivityScheduleSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->cronActivityScheduleFactory = $cronActivityScheduleFactory;
        $this->cronActivityScheduleCollectionFactory = $cronActivityScheduleCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataCronActivityScheduleFactory = $dataCronActivityScheduleFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Interprise\Logger\Api\Data\CronActivityScheduleInterface $cronActivitySchedule
    ) {
        /* if (empty($cronActivitySchedule->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $cronActivitySchedule->setStoreId($storeId);
        } */
        try {
            $this->resource->save($cronActivitySchedule);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the cronActivitySchedule: %1',
                $exception->getMessage()
            ));
        }
        return $cronActivitySchedule;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($cronActivityScheduleId)
    {
        $cronActivitySchedule = $this->cronActivityScheduleFactory->create();
        $this->resource->load($cronActivitySchedule, $cronActivityScheduleId);
        if (!$cronActivitySchedule->getId()) {
            throw new NoSuchEntityException(__('CronActivitySchedule with id "%1" does not exist.', $cronActivityScheduleId));
        }
        return $cronActivitySchedule;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->cronActivityScheduleCollectionFactory->create();
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
        \Interprise\Logger\Api\Data\CronActivityScheduleInterface $cronActivitySchedule
    ) {
        try {
            $this->resource->delete($cronActivitySchedule);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the CronActivitySchedule: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($cronActivityScheduleId)
    {
        return $this->delete($this->getById($cronActivityScheduleId));
    }
}

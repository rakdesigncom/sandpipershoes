<?php


namespace Interprise\Logger\Model;

use Interprise\Logger\Api\CronMasterRepositoryInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Exception\NoSuchEntityException;
use Interprise\Logger\Api\Data\CronMasterSearchResultsInterfaceFactory;
use Interprise\Logger\Api\Data\CronMasterInterfaceFactory;
use Interprise\Logger\Model\ResourceModel\CronMaster as ResourceCronMaster;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Store\Model\StoreManagerInterface;
use Interprise\Logger\Model\ResourceModel\CronMaster\CollectionFactory as CronMasterCollectionFactory;

class CronMasterRepository implements CronMasterRepositoryInterface
{

    protected $cronMasterCollectionFactory;

    protected $resource;

    protected $dataObjectHelper;

    private $storeManager;

    protected $dataCronMasterFactory;

    protected $cronMasterFactory;

    protected $searchResultsFactory;

    protected $dataObjectProcessor;
    /**
     * @param ResourceCronMaster $resource
     * @param CronMasterFactory $cronMasterFactory
     * @param CronMasterInterfaceFactory $dataCronMasterFactory
     * @param CronMasterCollectionFactory $cronMasterCollectionFactory
     * @param CronMasterSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceCronMaster $resource,
        CronMasterFactory $cronMasterFactory,
        CronMasterInterfaceFactory $dataCronMasterFactory,
        CronMasterCollectionFactory $cronMasterCollectionFactory,
        CronMasterSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->cronMasterFactory = $cronMasterFactory;
        $this->cronMasterCollectionFactory = $cronMasterCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataCronMasterFactory = $dataCronMasterFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Interprise\Logger\Api\Data\CronMasterInterface $cronMaster
    ) {
        /* if (empty($cronMaster->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $cronMaster->setStoreId($storeId);
        } */
        try {
            $this->resource->save($cronMaster);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the cronMaster: %1',
                $exception->getMessage()
            ));
        }
        return $cronMaster;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($cronMasterId)
    {
        $cronMaster = $this->cronMasterFactory->create();
        $this->resource->load($cronMaster, $cronMasterId);
        if (!$cronMaster->getId()) {
            throw new NoSuchEntityException(__('CronMaster with id "%1" does not exist.', $cronMasterId));
        }
        return $cronMaster;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->cronMasterCollectionFactory->create();
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
        \Interprise\Logger\Api\Data\CronMasterInterface $cronMaster
    ) {
        try {
            $this->resource->delete($cronMaster);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the CronMaster: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($cronMasterId)
    {
        return $this->delete($this->getById($cronMasterId));
    }
}

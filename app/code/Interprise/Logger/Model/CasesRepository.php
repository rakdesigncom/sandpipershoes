<?php


namespace Interprise\Logger\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Interprise\Logger\Model\ResourceModel\Cases as ResourceCases;
use Interprise\Logger\Api\Data\CasesSearchResultsInterfaceFactory;
use Interprise\Logger\Api\Data\CasesInterfaceFactory;
use Interprise\Logger\Api\CasesRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Interprise\Logger\Model\ResourceModel\Cases\CollectionFactory as CasesCollectionFactory;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;

class CasesRepository implements CasesRepositoryInterface
{
    protected $extensibleDataObjectConverter;
    protected $dataObjectProcessor;

    protected $resource;

    protected $dataCasesFactory;

    protected $dataObjectHelper;

    private $collectionProcessor;

    protected $extensionAttributesJoinProcessor;

    protected $caseFactory;

    protected $searchResultsFactory;

    protected $caseCollectionFactory;

    private $storeManager;
    /**
     * @param ResourceCases $resource
     * @param CasesFactory $caseFactory
     * @param CasesInterfaceFactory $dataCasesFactory
     * @param CasesCollectionFactory $caseCollectionFactory
     * @param CasesSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceCases $resource,
        CasesFactory $caseFactory,
        CasesInterfaceFactory $dataCasesFactory,
        CasesCollectionFactory $caseCollectionFactory,
        CasesSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->caseFactory = $caseFactory;
        $this->caseCollectionFactory = $caseCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataCasesFactory = $dataCasesFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Interprise\Logger\Api\Data\CasesInterface $case
    ) {
        /* if (empty($case->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $case->setStoreId($storeId);
        } */
        
        $caseData = $this->extensibleDataObjectConverter->toNestedArray(
            $case,
            [],
            \Interprise\Logger\Api\Data\CasesInterface::class
        );
        
        $caseModel = $this->caseFactory->create()->setData($caseData);
        
        try {
            $this->resource->save($caseModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the case: %1',
                $exception->getMessage()
            ));
        }
        return $caseModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getById($caseId)
    {
        $case = $this->caseFactory->create();
        $this->resource->load($case, $caseId);
        if (!$case->getId()) {
            throw new NoSuchEntityException(__('case with id "%1" does not exist.', $caseId));
        }
        return $case->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->caseCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Interprise\Logger\Api\Data\CasesInterface::class
        );
        
        $this->collectionProcessor->process($criteria, $collection);
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }
        
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Interprise\Logger\Api\Data\CasesInterface $case
    ) {
        try {
            $caseModel = $this->caseFactory->create();
            $this->resource->load($caseModel, $case->getCasesId());
            $this->resource->delete($caseModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the case: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($caseId)
    {
        return $this->delete($this->getById($caseId));
    }
}

<?php


namespace Interprise\Logger\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Interprise\Logger\Model\ResourceModel\Transactiondetail\CollectionFactory as TransactiondetailCollectionFactory;
use Interprise\Logger\Api\Data\TransactiondetailSearchResultsInterfaceFactory;
use Interprise\Logger\Api\Data\TransactiondetailInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Interprise\Logger\Api\TransactiondetailRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Interprise\Logger\Model\ResourceModel\Transactiondetail as ResourceTransactiondetail;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\ExtensibleDataObjectConverter;

class TransactiondetailRepository implements TransactiondetailRepositoryInterface
{

    private $storeManager;

    protected $resource;

    protected $dataObjectProcessor;

    protected $dataObjectHelper;

    private $collectionProcessor;

    protected $transactiondetailFactory;

    protected $transactiondetailCollectionFactory;

    protected $extensionAttributesJoinProcessor;

    protected $searchResultsFactory;

    protected $dataTransactiondetailFactory;

    protected $extensibleDataObjectConverter;

    /**
     * @param ResourceTransactiondetail $resource
     * @param TransactiondetailFactory $transactiondetailFactory
     * @param TransactiondetailInterfaceFactory $dataTransactiondetailFactory
     * @param TransactiondetailCollectionFactory $transactiondetailCollectionFactory
     * @param TransactiondetailSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceTransactiondetail $resource,
        TransactiondetailFactory $transactiondetailFactory,
        TransactiondetailInterfaceFactory $dataTransactiondetailFactory,
        TransactiondetailCollectionFactory $transactiondetailCollectionFactory,
        TransactiondetailSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->transactiondetailFactory = $transactiondetailFactory;
        $this->transactiondetailCollectionFactory = $transactiondetailCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataTransactiondetailFactory = $dataTransactiondetailFactory;
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
        \Interprise\Logger\Api\Data\TransactiondetailInterface $transactiondetail
    ) {
        /* if (empty($transactiondetail->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $transactiondetail->setStoreId($storeId);
        } */
        
        $transactiondetailData = $this->extensibleDataObjectConverter->toNestedArray(
            $transactiondetail,
            [],
            \Interprise\Logger\Api\Data\TransactiondetailInterface::class
        );
        
        $transactiondetailModel = $this->transactiondetailFactory->create()->setData($transactiondetailData);
        
        try {
            $this->resource->save($transactiondetailModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the transactiondetail: %1',
                $exception->getMessage()
            ));
        }
        return $transactiondetailModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getById($transactiondetailId)
    {
        $transactiondetail = $this->transactiondetailFactory->create();
        $this->resource->load($transactiondetail, $transactiondetailId);
        if (!$transactiondetail->getId()) {
            throw new NoSuchEntityException(__('transactiondetail with id "%1" does not exist.', $transactiondetailId));
        }
        return $transactiondetail->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->transactiondetailCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Interprise\Logger\Api\Data\TransactiondetailInterface::class
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
        \Interprise\Logger\Api\Data\TransactiondetailInterface $transactiondetail
    ) {
        try {
            $transactiondetailModel = $this->transactiondetailFactory->create();
            $this->resource->load($transactiondetailModel, $transactiondetail->getTransactiondetailId());
            $this->resource->delete($transactiondetailModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the transactiondetail: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($transactiondetailId)
    {
        return $this->delete($this->getById($transactiondetailId));
    }
}

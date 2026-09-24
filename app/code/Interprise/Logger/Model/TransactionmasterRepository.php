<?php


namespace Interprise\Logger\Model;

use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Interprise\Logger\Api\Data\TransactionmasterInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Interprise\Logger\Model\ResourceModel\Transactionmaster\CollectionFactory as TransactionmasterCollectionFactory;
use Magento\Framework\Reflection\DataObjectProcessor;
use Interprise\Logger\Api\TransactionmasterRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Interprise\Logger\Api\Data\TransactionmasterSearchResultsInterfaceFactory;
use Interprise\Logger\Model\ResourceModel\Transactionmaster as ResourceTransactionmaster;

class TransactionmasterRepository implements TransactionmasterRepositoryInterface
{

    protected $extensibleDataObjectConverter;
    protected $resource;

    protected $dataObjectProcessor;

    protected $dataObjectHelper;

    private $collectionProcessor;

    protected $extensionAttributesJoinProcessor;

    protected $transactionmasterCollectionFactory;

    protected $transactionmasterFactory;

    protected $searchResultsFactory;

    protected $dataTransactionmasterFactory;

    private $storeManager;
    /**
     * @param ResourceTransactionmaster $resource
     * @param TransactionmasterFactory $transactionmasterFactory
     * @param TransactionmasterInterfaceFactory $dataTransactionmasterFactory
     * @param TransactionmasterCollectionFactory $transactionmasterCollectionFactory
     * @param TransactionmasterSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceTransactionmaster $resource,
        TransactionmasterFactory $transactionmasterFactory,
        TransactionmasterInterfaceFactory $dataTransactionmasterFactory,
        TransactionmasterCollectionFactory $transactionmasterCollectionFactory,
        TransactionmasterSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->transactionmasterFactory = $transactionmasterFactory;
        $this->transactionmasterCollectionFactory = $transactionmasterCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataTransactionmasterFactory = $dataTransactionmasterFactory;
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
        \Interprise\Logger\Api\Data\TransactionmasterInterface $transactionmaster
    ) {
        /* if (empty($transactionmaster->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $transactionmaster->setStoreId($storeId);
        } */
        
        $transactionmasterData = $this->extensibleDataObjectConverter->toNestedArray(
            $transactionmaster,
            [],
            \Interprise\Logger\Api\Data\TransactionmasterInterface::class
        );
        
        $transactionmasterModel = $this->transactionmasterFactory->create()->setData($transactionmasterData);
        
        try {
            $this->resource->save($transactionmasterModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the transactionmaster: %1',
                $exception->getMessage()
            ));
        }
        return $transactionmasterModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getById($transactionmasterId)
    {
        $transactionmaster = $this->transactionmasterFactory->create();
        $this->resource->load($transactionmaster, $transactionmasterId);
        if (!$transactionmaster->getId()) {
            throw new NoSuchEntityException(__('transactionmaster with id "%1" does not exist.', $transactionmasterId));
        }
        return $transactionmaster->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->transactionmasterCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Interprise\Logger\Api\Data\TransactionmasterInterface::class
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
        \Interprise\Logger\Api\Data\TransactionmasterInterface $transactionmaster
    ) {
        try {
            $transactionmasterModel = $this->transactionmasterFactory->create();
            $this->resource->load($transactionmasterModel, $transactionmaster->getTransactionmasterId());
            $this->resource->delete($transactionmasterModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the transactionmaster: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($transactionmasterId)
    {
        return $this->delete($this->getById($transactionmasterId));
    }
}

<?php


namespace Interprise\Logger\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Interprise\Logger\Model\ResourceModel\Statementaccount\CollectionFactory as StatementaccountCollectionFactory;
use Interprise\Logger\Api\StatementaccountRepositoryInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Store\Model\StoreManagerInterface;
use Interprise\Logger\Model\ResourceModel\Statementaccount as ResourceStatementaccount;
use Magento\Framework\Exception\NoSuchEntityException;
use Interprise\Logger\Api\Data\StatementaccountSearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Interprise\Logger\Api\Data\StatementaccountInterfaceFactory;

class StatementaccountRepository implements StatementaccountRepositoryInterface
{
    protected $extensibleDataObjectConverter;
    protected $statementaccountCollectionFactory;

    protected $resource;

    protected $dataObjectProcessor;

    protected $dataObjectHelper;

    private $collectionProcessor;

    protected $statementaccountFactory;

    protected $extensionAttributesJoinProcessor;

    protected $dataStatementaccountFactory;

    protected $searchResultsFactory;

    private $storeManager;
    /**
     * @param ResourceStatementaccount $resource
     * @param StatementaccountFactory $statementaccountFactory
     * @param StatementaccountInterfaceFactory $dataStatementaccountFactory
     * @param StatementaccountCollectionFactory $statementaccountCollectionFactory
     * @param StatementaccountSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceStatementaccount $resource,
        StatementaccountFactory $statementaccountFactory,
        StatementaccountInterfaceFactory $dataStatementaccountFactory,
        StatementaccountCollectionFactory $statementaccountCollectionFactory,
        StatementaccountSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->statementaccountFactory = $statementaccountFactory;
        $this->statementaccountCollectionFactory = $statementaccountCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataStatementaccountFactory = $dataStatementaccountFactory;
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
        \Interprise\Logger\Api\Data\StatementaccountInterface $statementaccount
    ) {
        /* if (empty($statementaccount->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $statementaccount->setStoreId($storeId);
        } */
        
        $statementaccountData = $this->extensibleDataObjectConverter->toNestedArray(
            $statementaccount,
            [],
            \Interprise\Logger\Api\Data\StatementaccountInterface::class
        );
        
        $statementaccountModel = $this->statementaccountFactory->create()->setData($statementaccountData);
        
        try {
            $this->resource->save($statementaccountModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the statementaccount: %1',
                $exception->getMessage()
            ));
        }
        return $statementaccountModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getById($statementaccountId)
    {
        $statementaccount = $this->statementaccountFactory->create();
        $this->resource->load($statementaccount, $statementaccountId);
        if (!$statementaccount->getId()) {
            throw new NoSuchEntityException(__('statementaccount with id "%1" does not exist.', $statementaccountId));
        }
        return $statementaccount->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->statementaccountCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Interprise\Logger\Api\Data\StatementaccountInterface::class
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
        \Interprise\Logger\Api\Data\StatementaccountInterface $statementaccount
    ) {
        try {
            $statementaccountModel = $this->statementaccountFactory->create();
            $this->resource->load($statementaccountModel, $statementaccount->getStatementaccountId());
            $this->resource->delete($statementaccountModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the statementaccount: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($statementaccountId)
    {
        return $this->delete($this->getById($statementaccountId));
    }
}

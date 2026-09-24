<?php


namespace Interprise\Logger\Model;

use Interprise\Logger\Api\PricingcustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Interprise\Logger\Model\ResourceModel\Pricingcustomer\CollectionFactory as PricingcustomerCollectionFactory;
use Interprise\Logger\Api\Data\PricingcustomerSearchResultsInterfaceFactory;
use Interprise\Logger\Model\ResourceModel\Pricingcustomer as ResourcePricingcustomer;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Interprise\Logger\Api\Data\PricingcustomerInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;

class PricingcustomerRepository implements PricingcustomerRepositoryInterface
{

    protected $extensibleDataObjectConverter;
    protected $dataObjectProcessor;

    protected $resource;

    protected $pricingcustomerFactory;

    protected $pricingcustomerCollectionFactory;

    protected $dataObjectHelper;

    private $collectionProcessor;

    protected $extensionAttributesJoinProcessor;

    protected $dataPricingcustomerFactory;

    protected $searchResultsFactory;

    private $storeManager;
    /**
     * @param ResourcePricingcustomer $resource
     * @param PricingcustomerFactory $pricingcustomerFactory
     * @param PricingcustomerInterfaceFactory $dataPricingcustomerFactory
     * @param PricingcustomerCollectionFactory $pricingcustomerCollectionFactory
     * @param PricingcustomerSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourcePricingcustomer $resource,
        PricingcustomerFactory $pricingcustomerFactory,
        PricingcustomerInterfaceFactory $dataPricingcustomerFactory,
        PricingcustomerCollectionFactory $pricingcustomerCollectionFactory,
        PricingcustomerSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->pricingcustomerFactory = $pricingcustomerFactory;
        $this->pricingcustomerCollectionFactory = $pricingcustomerCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPricingcustomerFactory = $dataPricingcustomerFactory;
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
        \Interprise\Logger\Api\Data\PricingcustomerInterface $pricingcustomer
    ) {
        /* if (empty($pricingcustomer->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $pricingcustomer->setStoreId($storeId);
        } */
        
        $pricingcustomerData = $this->extensibleDataObjectConverter->toNestedArray(
            $pricingcustomer,
            [],
            \Interprise\Logger\Api\Data\PricingcustomerInterface::class
        );
        
        $pricingcustomerModel = $this->pricingcustomerFactory->create()->setData($pricingcustomerData);
        
        try {
            $this->resource->save($pricingcustomerModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the pricingcustomer: %1',
                $exception->getMessage()
            ));
        }
        return $pricingcustomerModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getById($pricingcustomerId)
    {
        $pricingcustomer = $this->pricingcustomerFactory->create();
        $this->resource->load($pricingcustomer, $pricingcustomerId);
        if (!$pricingcustomer->getId()) {
            throw new NoSuchEntityException(__('pricingcustomer with id "%1" does not exist.', $pricingcustomerId));
        }
        return $pricingcustomer->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->pricingcustomerCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Interprise\Logger\Api\Data\PricingcustomerInterface::class
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
        \Interprise\Logger\Api\Data\PricingcustomerInterface $pricingcustomer
    ) {
        try {
            $pricingcustomerModel = $this->pricingcustomerFactory->create();
            $this->resource->load($pricingcustomerModel, $pricingcustomer->getPricingcustomerId());
            $this->resource->delete($pricingcustomerModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the pricingcustomer: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($pricingcustomerId)
    {
        return $this->delete($this->getById($pricingcustomerId));
    }
}

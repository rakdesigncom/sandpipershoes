<?php


namespace Interprise\Logger\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Interprise\Logger\Api\PricelistsRepositoryInterface;
use Interprise\Logger\Model\ResourceModel\Pricelists as ResourcePricelists;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Interprise\Logger\Model\ResourceModel\Pricelists\CollectionFactory as PricelistsCollectionFactory;
use Interprise\Logger\Api\Data\PricelistsSearchResultsInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Interprise\Logger\Api\Data\PricelistsInterfaceFactory;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\ExtensibleDataObjectConverter;

class PricelistsRepository implements PricelistsRepositoryInterface
{

    private $storeManager;

    protected $pricelistsFactory;

    protected $resource;

    protected $dataObjectProcessor;

    protected $dataObjectHelper;

    private $collectionProcessor;

    protected $extensionAttributesJoinProcessor;

    protected $dataPricelistsFactory;

    protected $pricelistsCollectionFactory;

    protected $searchResultsFactory;

    protected $extensibleDataObjectConverter;

    /**
     * @param ResourcePricelists $resource
     * @param PricelistsFactory $pricelistsFactory
     * @param PricelistsInterfaceFactory $dataPricelistsFactory
     * @param PricelistsCollectionFactory $pricelistsCollectionFactory
     * @param PricelistsSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourcePricelists $resource,
        PricelistsFactory $pricelistsFactory,
        PricelistsInterfaceFactory $dataPricelistsFactory,
        PricelistsCollectionFactory $pricelistsCollectionFactory,
        PricelistsSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->pricelistsFactory = $pricelistsFactory;
        $this->pricelistsCollectionFactory = $pricelistsCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPricelistsFactory = $dataPricelistsFactory;
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
        \Interprise\Logger\Api\Data\PricelistsInterface $pricelists
    ) {
        /* if (empty($pricelists->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $pricelists->setStoreId($storeId);
        } */
        
        $pricelistsData = $this->extensibleDataObjectConverter->toNestedArray(
            $pricelists,
            [],
            \Interprise\Logger\Api\Data\PricelistsInterface::class
        );
        
        $pricelistsModel = $this->pricelistsFactory->create()->setData($pricelistsData);
        
        try {
            $this->resource->save($pricelistsModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the pricelists: %1',
                $exception->getMessage()
            ));
        }
        return $pricelistsModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getById($pricelistsId)
    {
        $pricelists = $this->pricelistsFactory->create();
        $this->resource->load($pricelists, $pricelistsId);
        if (!$pricelists->getId()) {
            throw new NoSuchEntityException(__('pricelists with id "%1" does not exist.', $pricelistsId));
        }
        return $pricelists->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->pricelistsCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Interprise\Logger\Api\Data\PricelistsInterface::class
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
        \Interprise\Logger\Api\Data\PricelistsInterface $pricelists
    ) {
        try {
            $pricelistsModel = $this->pricelistsFactory->create();
            $this->resource->load($pricelistsModel, $pricelists->getPricelistsId());
            $this->resource->delete($pricelistsModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the pricelists: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($pricelistsId)
    {
        return $this->delete($this->getById($pricelistsId));
    }
}

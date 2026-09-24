<?php


namespace Interprise\Logger\Model;

use Interprise\Logger\Api\CountryclassmappingRepositoryInterface;
use Interprise\Logger\Api\Data\CountryclassmappingSearchResultsInterfaceFactory;
use Interprise\Logger\Api\Data\CountryclassmappingInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Interprise\Logger\Model\ResourceModel\Countryclassmapping as ResourceCountryclassmapping;
use Interprise\Logger\Model\ResourceModel\Countryclassmapping\CollectionFactory as CountryclassmappingCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\ExtensibleDataObjectConverter;

class CountryclassmappingRepository implements CountryclassmappingRepositoryInterface
{

    protected $resource;

    protected $countryclassmappingFactory;

    protected $countryclassmappingCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataCountryclassmappingFactory;

    protected $extensionAttributesJoinProcessor;

    private $storeManager;

    private $collectionProcessor;

    protected $extensibleDataObjectConverter;

    /**
     * @param ResourceCountryclassmapping $resource
     * @param CountryclassmappingFactory $countryclassmappingFactory
     * @param CountryclassmappingInterfaceFactory $dataCountryclassmappingFactory
     * @param CountryclassmappingCollectionFactory $countryclassmappingCollectionFactory
     * @param CountryclassmappingSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceCountryclassmapping $resource,
        CountryclassmappingFactory $countryclassmappingFactory,
        CountryclassmappingInterfaceFactory $dataCountryclassmappingFactory,
        CountryclassmappingCollectionFactory $countryclassmappingCollectionFactory,
        CountryclassmappingSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->countryclassmappingFactory = $countryclassmappingFactory;
        $this->countryclassmappingCollectionFactory = $countryclassmappingCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataCountryclassmappingFactory = $dataCountryclassmappingFactory;
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
        \Interprise\Logger\Api\Data\CountryclassmappingInterface $countryclassmapping
    ) {
        /* if (empty($countryclassmapping->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $countryclassmapping->setStoreId($storeId);
        } */
        
        $countryclassmappingData = $this->extensibleDataObjectConverter->toNestedArray(
            $countryclassmapping,
            [],
            \Interprise\Logger\Api\Data\CountryclassmappingInterface::class
        );
        
        $countryclassmappingModel = $this->countryclassmappingFactory->create()->setData($countryclassmappingData);
        
        try {
            $this->resource->save($countryclassmappingModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the countryclassmapping: %1',
                $exception->getMessage()
            ));
        }
        return $countryclassmappingModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getById($countryclassmappingId)
    {
        $countryclassmapping = $this->countryclassmappingFactory->create();
        $this->resource->load($countryclassmapping, $countryclassmappingId);
        if (!$countryclassmapping->getId()) {
            throw new NoSuchEntityException(__('countryclassmapping with id "%1" does not exist.', $countryclassmappingId));
        }
        return $countryclassmapping->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->countryclassmappingCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Interprise\Logger\Api\Data\CountryclassmappingInterface::class
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
        \Interprise\Logger\Api\Data\CountryclassmappingInterface $countryclassmapping
    ) {
        try {
            $countryclassmappingModel = $this->countryclassmappingFactory->create();
            $this->resource->load($countryclassmappingModel, $countryclassmapping->getCountryclassmappingId());
            $this->resource->delete($countryclassmappingModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the countryclassmapping: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($countryclassmappingId)
    {
        return $this->delete($this->getById($countryclassmappingId));
    }
}

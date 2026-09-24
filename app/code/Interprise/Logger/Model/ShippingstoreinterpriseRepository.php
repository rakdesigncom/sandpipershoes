<?php


namespace Interprise\Logger\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Interprise\Logger\Api\Data\ShippingstoreinterpriseSearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Store\Model\StoreManagerInterface;
use Interprise\Logger\Api\ShippingstoreinterpriseRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Interprise\Logger\Model\ResourceModel\Shippingstoreinterprise as ResourceShippingstoreinterprise;
use Magento\Framework\Api\DataObjectHelper;
use Interprise\Logger\Api\Data\ShippingstoreinterpriseInterfaceFactory;
use Magento\Framework\Reflection\DataObjectProcessor;
use Interprise\Logger\Model\ResourceModel\Shippingstoreinterprise\CollectionFactory as ShippingsCollectionFactory;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\ExtensibleDataObjectConverter;

class ShippingstoreinterpriseRepository implements ShippingstoreinterpriseRepositoryInterface
{

    private $storeManager;

    protected $dataShippingstoreinterpriseFactory;

    protected $shippingstoreinterpriseCollectionFactory;

    protected $resource;

    protected $dataObjectProcessor;

    protected $dataObjectHelper;

    private $collectionProcessor;

    protected $extensionAttributesJoinProcessor;

    protected $shippingstoreinterpriseFactory;

    protected $searchResultsFactory;

    protected $extensibleDataObjectConverter;

    /**
     * @param ResourceShippingstoreinterprise $resource
     * @param ShippingstoreinterpriseFactory $shippingstoreinterpriseFactory
     * @param ShippingstoreinterpriseInterfaceFactory $dataShippingstoreinterpriseFactory
     * @param ShippingsCollectionFactory $shippingstoreinterpriseCollectionFactory
     * @param ShippingstoreinterpriseSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceShippingstoreinterprise $resource,
        ShippingstoreinterpriseFactory $shippingstoreinterpriseFactory,
        ShippingstoreinterpriseInterfaceFactory $dataShippingstoreinterpriseFactory,
        ShippingsCollectionFactory $shippingstoreinterpriseCollectionFactory,
        ShippingstoreinterpriseSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->shippingstoreinterpriseFactory = $shippingstoreinterpriseFactory;
        $this->shippingstoreinterpriseCollectionFactory = $shippingstoreinterpriseCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataShippingstoreinterpriseFactory = $dataShippingstoreinterpriseFactory;
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
        \Interprise\Logger\Api\Data\ShippingstoreinterpriseInterface $shippingstoreinterprise
    ) {
        /* if (empty($shippingstoreinterprise->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $shippingstoreinterprise->setStoreId($storeId);
        } */
        
        $shippingstoreinterpriseData = $this->extensibleDataObjectConverter->toNestedArray(
            $shippingstoreinterprise,
            [],
            \Interprise\Logger\Api\Data\ShippingstoreinterpriseInterface::class
        );
        
        $shippingstoreinterpriseModel = $this->shippingstoreinterpriseFactory->create()
                                        ->setData($shippingstoreinterpriseData);
        
        try {
            $this->resource->save($shippingstoreinterpriseModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the shippingstoreinterprise: %1',
                $exception->getMessage()
            ));
        }
        return $shippingstoreinterpriseModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getById($shippingstoreinterpriseId)
    {
        $shippingstoreinterprise = $this->shippingstoreinterpriseFactory->create();
        $this->resource->load($shippingstoreinterprise, $shippingstoreinterpriseId);
        if (!$shippingstoreinterprise->getId()) {
            throw new NoSuchEntityException(__('shippingstoreinterprise with id "%1" does not exist.', $shippingstoreinterpriseId));
        }
        return $shippingstoreinterprise->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->shippingstoreinterpriseCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Interprise\Logger\Api\Data\ShippingstoreinterpriseInterface::class
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
        \Interprise\Logger\Api\Data\ShippingstoreinterpriseInterface $shippingstoreinterprise
    ) {
        try {
            $shippingstoreinterpriseModel = $this->shippingstoreinterpriseFactory->create();
            $this->resource->load(
                $shippingstoreinterpriseModel,
                $shippingstoreinterprise->getShippingstoreinterpriseId()
            );
            $this->resource->delete($shippingstoreinterpriseModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the shippingstoreinterprise: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($shippingstoreinterpriseId)
    {
        return $this->delete($this->getById($shippingstoreinterpriseId));
    }
}

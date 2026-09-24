<?php


namespace Interprise\Logger\Model;

use Interprise\Logger\Api\Data\InstallwizardInterfaceFactory;
use Interprise\Logger\Model\ResourceModel\Installwizard\CollectionFactory as InstallwizardCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Interprise\Logger\Api\InstallwizardRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Api\DataObjectHelper;
use Interprise\Logger\Model\ResourceModel\Installwizard as ResourceInstallwizard;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Interprise\Logger\Api\Data\InstallwizardSearchResultsInterfaceFactory;

class InstallwizardRepository implements InstallwizardRepositoryInterface
{

    private $storeManager;

    protected $resource;

    protected $dataObjectProcessor;

    protected $installwizardFactory;

    protected $dataObjectHelper;

    protected $installwizardCollectionFactory;

    private $collectionProcessor;

    protected $extensionAttributesJoinProcessor;

    protected $searchResultsFactory;

    protected $dataInstallwizardFactory;

    protected $extensibleDataObjectConverter;

    /**
     * @param ResourceInstallwizard $resource
     * @param InstallwizardFactory $installwizardFactory
     * @param InstallwizardInterfaceFactory $dataInstallwizardFactory
     * @param InstallwizardCollectionFactory $installwizardCollectionFactory
     * @param InstallwizardSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceInstallwizard $resource,
        InstallwizardFactory $installwizardFactory,
        InstallwizardInterfaceFactory $dataInstallwizardFactory,
        InstallwizardCollectionFactory $installwizardCollectionFactory,
        InstallwizardSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->installwizardFactory = $installwizardFactory;
        $this->installwizardCollectionFactory = $installwizardCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataInstallwizardFactory = $dataInstallwizardFactory;
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
        \Interprise\Logger\Api\Data\InstallwizardInterface $installwizard
    ) {
        /* if (empty($installwizard->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $installwizard->setStoreId($storeId);
        } */
        
        $installwizardData = $this->extensibleDataObjectConverter->toNestedArray(
            $installwizard,
            [],
            \Interprise\Logger\Api\Data\InstallwizardInterface::class
        );
        
        $installwizardModel = $this->installwizardFactory->create()->setData($installwizardData);
        
        try {
            $this->resource->save($installwizardModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the installwizard: %1',
                $exception->getMessage()
            ));
        }
        return $installwizardModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getById($installwizardId)
    {
        $installwizard = $this->installwizardFactory->create();
        $this->resource->load($installwizard, $installwizardId);
        if (!$installwizard->getId()) {
            throw new NoSuchEntityException(__('installwizard with id "%1" does not exist.', $installwizardId));
        }
        return $installwizard->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->installwizardCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Interprise\Logger\Api\Data\InstallwizardInterface::class
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
        \Interprise\Logger\Api\Data\InstallwizardInterface $installwizard
    ) {
        try {
            $installwizardModel = $this->installwizardFactory->create();
            $this->resource->load($installwizardModel, $installwizard->getInstallwizardId());
            $this->resource->delete($installwizardModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the installwizard: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($installwizardId)
    {
        return $this->delete($this->getById($installwizardId));
    }
}

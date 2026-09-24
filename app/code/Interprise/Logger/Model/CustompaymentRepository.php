<?php


namespace Interprise\Logger\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Interprise\Logger\Api\Data\CustompaymentSearchResultsInterfaceFactory;
use Interprise\Logger\Api\Data\CustompaymentInterfaceFactory;
use Interprise\Logger\Model\ResourceModel\Custompayment\CollectionFactory as CustompaymentCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Interprise\Logger\Model\ResourceModel\Custompayment as ResourceCustompayment;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Interprise\Logger\Api\CustompaymentRepositoryInterface;

class CustompaymentRepository implements CustompaymentRepositoryInterface
{

    protected $extensibleDataObjectConverter;
    protected $resource;

    protected $dataObjectProcessor;

    protected $dataCustompaymentFactory;

    protected $dataObjectHelper;

    private $collectionProcessor;

    protected $extensionAttributesJoinProcessor;

    protected $custompaymentFactory;

    protected $searchResultsFactory;

    protected $custompaymentCollectionFactory;

    private $storeManager;
    /**
     * @param ResourceCustompayment $resource
     * @param CustompaymentFactory $custompaymentFactory
     * @param CustompaymentInterfaceFactory $dataCustompaymentFactory
     * @param CustompaymentCollectionFactory $custompaymentCollectionFactory
     * @param CustompaymentSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceCustompayment $resource,
        CustompaymentFactory $custompaymentFactory,
        CustompaymentInterfaceFactory $dataCustompaymentFactory,
        CustompaymentCollectionFactory $custompaymentCollectionFactory,
        CustompaymentSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->custompaymentFactory = $custompaymentFactory;
        $this->custompaymentCollectionFactory = $custompaymentCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataCustompaymentFactory = $dataCustompaymentFactory;
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
        \Interprise\Logger\Api\Data\CustompaymentInterface $custompayment
    ) {
        /* if (empty($custompayment->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $custompayment->setStoreId($storeId);
        } */
        
        $custompaymentData = $this->extensibleDataObjectConverter->toNestedArray(
            $custompayment,
            [],
            \Interprise\Logger\Api\Data\CustompaymentInterface::class
        );
        
        $custompaymentModel = $this->custompaymentFactory->create()->setData($custompaymentData);
        
        try {
            $this->resource->save($custompaymentModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the custompayment: %1',
                $exception->getMessage()
            ));
        }
        return $custompaymentModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getById($custompaymentId)
    {
        $custompayment = $this->custompaymentFactory->create();
        $this->resource->load($custompayment, $custompaymentId);
        if (!$custompayment->getId()) {
            throw new NoSuchEntityException(__('custompayment with id "%1" does not exist.', $custompaymentId));
        }
        return $custompayment->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->custompaymentCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Interprise\Logger\Api\Data\CustompaymentInterface::class
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
        \Interprise\Logger\Api\Data\CustompaymentInterface $custompayment
    ) {
        try {
            $custompaymentModel = $this->custompaymentFactory->create();
            $this->resource->load($custompaymentModel, $custompayment->getCustompaymentId());
            $this->resource->delete($custompaymentModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the custompayment: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($custompaymentId)
    {
        return $this->delete($this->getById($custompaymentId));
    }
}

<?php


namespace Interprise\Logger\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Interprise\Logger\Model\ResourceModel\Paymentmethod as ResourcePaymentmethod;
use Interprise\Logger\Model\ResourceModel\Paymentmethod\CollectionFactory as PaymentmethodCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Interprise\Logger\Api\PaymentmethodRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Interprise\Logger\Api\Data\PaymentmethodInterfaceFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Interprise\Logger\Api\Data\PaymentmethodSearchResultsInterfaceFactory;

class PaymentmethodRepository implements PaymentmethodRepositoryInterface
{

    private $storeManager;

    protected $resource;

    protected $dataObjectProcessor;

    protected $dataObjectHelper;

    private $collectionProcessor;

    protected $extensionAttributesJoinProcessor;

    protected $dataPaymentmethodFactory;

    protected $paymentmethodFactory;

    protected $paymentmethodCollectionFactory;

    protected $searchResultsFactory;

    protected $extensibleDataObjectConverter;

    /**
     * @param ResourcePaymentmethod $resource
     * @param PaymentmethodFactory $paymentmethodFactory
     * @param PaymentmethodInterfaceFactory $dataPaymentmethodFactory
     * @param PaymentmethodCollectionFactory $paymentmethodCollectionFactory
     * @param PaymentmethodSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourcePaymentmethod $resource,
        PaymentmethodFactory $paymentmethodFactory,
        PaymentmethodInterfaceFactory $dataPaymentmethodFactory,
        PaymentmethodCollectionFactory $paymentmethodCollectionFactory,
        PaymentmethodSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->paymentmethodFactory = $paymentmethodFactory;
        $this->paymentmethodCollectionFactory = $paymentmethodCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPaymentmethodFactory = $dataPaymentmethodFactory;
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
        \Interprise\Logger\Api\Data\PaymentmethodInterface $paymentmethod
    ) {
        /* if (empty($paymentmethod->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $paymentmethod->setStoreId($storeId);
        } */
        
        $paymentmethodData = $this->extensibleDataObjectConverter->toNestedArray(
            $paymentmethod,
            [],
            \Interprise\Logger\Api\Data\PaymentmethodInterface::class
        );
        
        $paymentmethodModel = $this->paymentmethodFactory->create()->setData($paymentmethodData);
        
        try {
            $this->resource->save($paymentmethodModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the paymentmethod: %1',
                $exception->getMessage()
            ));
        }
        return $paymentmethodModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getById($paymentmethodId)
    {
        $paymentmethod = $this->paymentmethodFactory->create();
        $this->resource->load($paymentmethod, $paymentmethodId);
        if (!$paymentmethod->getId()) {
            throw new NoSuchEntityException(__('paymentmethod with id "%1" does not exist.', $paymentmethodId));
        }
        return $paymentmethod->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->paymentmethodCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Interprise\Logger\Api\Data\PaymentmethodInterface::class
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
        \Interprise\Logger\Api\Data\PaymentmethodInterface $paymentmethod
    ) {
        try {
            $paymentmethodModel = $this->paymentmethodFactory->create();
            $this->resource->load($paymentmethodModel, $paymentmethod->getPaymentmethodId());
            $this->resource->delete($paymentmethodModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the paymentmethod: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($paymentmethodId)
    {
        return $this->delete($this->getById($paymentmethodId));
    }
}

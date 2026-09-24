<?php


namespace Interprise\Logger\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Interprise\Logger\Api\Data\CustompaymentitemInterfaceFactory;
use Interprise\Logger\Api\CustompaymentitemRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Interprise\Logger\Model\ResourceModel\Custompaymentitem as ResourceCustompaymentitem;
use Interprise\Logger\Model\ResourceModel\Custompaymentitem\CollectionFactory as CustompaymentitemCollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Interprise\Logger\Api\Data\CustompaymentitemSearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;

class CustompaymentitemRepository implements CustompaymentitemRepositoryInterface
{

    protected $extensibleDataObjectConverter;
    protected $resource;

    protected $dataObjectProcessor;

    protected $dataObjectHelper;

    private $collectionProcessor;

    protected $extensionAttributesJoinProcessor;

    protected $custompaymentitemFactory;

    protected $custompaymentitemCollectionFactory;

    protected $dataCustompaymentitemFactory;

    protected $searchResultsFactory;

    private $storeManager;
    /**
     * @param ResourceCustompaymentitem $resource
     * @param CustompaymentitemFactory $custompaymentitemFactory
     * @param CustompaymentitemInterfaceFactory $dataCustompaymentitemFactory
     * @param CustompaymentitemCollectionFactory $custompaymentitemCollectionFactory
     * @param CustompaymentitemSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceCustompaymentitem $resource,
        CustompaymentitemFactory $custompaymentitemFactory,
        CustompaymentitemInterfaceFactory $dataCustompaymentitemFactory,
        CustompaymentitemCollectionFactory $custompaymentitemCollectionFactory,
        CustompaymentitemSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->custompaymentitemFactory = $custompaymentitemFactory;
        $this->custompaymentitemCollectionFactory = $custompaymentitemCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataCustompaymentitemFactory = $dataCustompaymentitemFactory;
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
        \Interprise\Logger\Api\Data\CustompaymentitemInterface $custompaymentitem
    ) {
        /* if (empty($custompaymentitem->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $custompaymentitem->setStoreId($storeId);
        } */
        
        $custompaymentitemData = $this->extensibleDataObjectConverter->toNestedArray(
            $custompaymentitem,
            [],
            \Interprise\Logger\Api\Data\CustompaymentitemInterface::class
        );
        
        $custompaymentitemModel = $this->custompaymentitemFactory->create()->setData($custompaymentitemData);
        
        try {
            $this->resource->save($custompaymentitemModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the custompaymentitem: %1',
                $exception->getMessage()
            ));
        }
        return $custompaymentitemModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getById($custompaymentitemId)
    {
        $custompaymentitem = $this->custompaymentitemFactory->create();
        $this->resource->load($custompaymentitem, $custompaymentitemId);
        if (!$custompaymentitem->getId()) {
            throw new NoSuchEntityException(__('custompaymentitem with id "%1" does not exist.', $custompaymentitemId));
        }
        return $custompaymentitem->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->custompaymentitemCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Interprise\Logger\Api\Data\CustompaymentitemInterface::class
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
        \Interprise\Logger\Api\Data\CustompaymentitemInterface $custompaymentitem
    ) {
        try {
            $custompaymentitemModel = $this->custompaymentitemFactory->create();
            $this->resource->load($custompaymentitemModel, $custompaymentitem->getCustompaymentitemId());
            $this->resource->delete($custompaymentitemModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the custompaymentitem: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($custompaymentitemId)
    {
        return $this->delete($this->getById($custompaymentitemId));
    }
}

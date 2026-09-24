<?php


namespace Interprise\Logger\Model;

use Magento\Framework\Api\DataObjectHelper;
use Interprise\Logger\Api\Data\PricelistsInterface;
use Interprise\Logger\Api\Data\PricelistsInterfaceFactory;

class Pricelists extends \Magento\Framework\Model\AbstractModel
{

    protected $dataObjectHelper;

    protected $pricelistsDataFactory;

    protected $_eventPrefix = 'interprise_price_lists';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param PricelistsInterfaceFactory $pricelistsDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Interprise\Logger\Model\ResourceModel\Pricelists $resource
     * @param \Interprise\Logger\Model\ResourceModel\Pricelists\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        PricelistsInterfaceFactory $pricelistsDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Interprise\Logger\Model\ResourceModel\Pricelists $resource,
        \Interprise\Logger\Model\ResourceModel\Pricelists\Collection $resourceCollection,
        array $data = []
    ) {
        $this->pricelistsDataFactory = $pricelistsDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve pricelists model with pricelists data
     * @return PricelistsInterface
     */
    public function getDataModel()
    {
        $pricelistsData = $this->getData();
        
        $pricelistsDataObject = $this->pricelistsDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $pricelistsDataObject,
            $pricelistsData,
            PricelistsInterface::class
        );
        
        return $pricelistsDataObject;
    }
}

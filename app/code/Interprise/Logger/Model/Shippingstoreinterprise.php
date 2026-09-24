<?php


namespace Interprise\Logger\Model;

use Magento\Framework\Api\DataObjectHelper;
use Interprise\Logger\Api\Data\ShippingstoreinterpriseInterface;
use Interprise\Logger\Api\Data\ShippingstoreinterpriseInterfaceFactory;

class Shippingstoreinterprise extends \Magento\Framework\Model\AbstractModel
{

    protected $dataObjectHelper;

    protected $shippingstoreinterpriseDataFactory;

    protected $_eventPrefix = 'interprise_shipping_store_interprise';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ShippingstoreinterpriseInterfaceFactory $shippingstoreinterpriseDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Interprise\Logger\Model\ResourceModel\Shippingstoreinterprise $resource
     * @param \Interprise\Logger\Model\ResourceModel\Shippingstoreinterprise\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ShippingstoreinterpriseInterfaceFactory $shippingstoreinterpriseDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Interprise\Logger\Model\ResourceModel\Shippingstoreinterprise $resource,
        \Interprise\Logger\Model\ResourceModel\Shippingstoreinterprise\Collection $resourceCollection,
        array $data = []
    ) {
        $this->shippingstoreinterpriseDataFactory = $shippingstoreinterpriseDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve shippingstoreinterprise model with shippingstoreinterprise data
     * @return ShippingstoreinterpriseInterface
     */
    public function getDataModel()
    {
        $shippingstoreinterpriseData = $this->getData();
        
        $shippingstoreinterpriseDataObject = $this->shippingstoreinterpriseDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $shippingstoreinterpriseDataObject,
            $shippingstoreinterpriseData,
            ShippingstoreinterpriseInterface::class
        );
        
        return $shippingstoreinterpriseDataObject;
    }
}

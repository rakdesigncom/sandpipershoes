<?php


namespace Interprise\Logger\Model;

use Interprise\Logger\Api\Data\PricingcustomerInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Interprise\Logger\Api\Data\PricingcustomerInterface;

class Pricingcustomer extends \Magento\Framework\Model\AbstractModel
{

    protected $dataObjectHelper;

    protected $_eventPrefix = 'interprise_pricing_customer';
    protected $pricingcustomerDataFactory;
    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param PricingcustomerInterfaceFactory $pricingcustomerDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Interprise\Logger\Model\ResourceModel\Pricingcustomer $resource
     * @param \Interprise\Logger\Model\ResourceModel\Pricingcustomer\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        PricingcustomerInterfaceFactory $pricingcustomerDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Interprise\Logger\Model\ResourceModel\Pricingcustomer $resource,
        \Interprise\Logger\Model\ResourceModel\Pricingcustomer\Collection $resourceCollection,
        array $data = []
    ) {
        $this->pricingcustomerDataFactory = $pricingcustomerDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve pricingcustomer model with pricingcustomer data
     * @return PricingcustomerInterface
     */
    public function getDataModel()
    {
        $pricingcustomerData = $this->getData();
        
        $pricingcustomerDataObject = $this->pricingcustomerDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $pricingcustomerDataObject,
            $pricingcustomerData,
            PricingcustomerInterface::class
        );
        
        return $pricingcustomerDataObject;
    }
}

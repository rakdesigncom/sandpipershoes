<?php


namespace Interprise\Logger\Model;

use Interprise\Logger\Api\Data\PaymentmethodInterface;
use Magento\Framework\Api\DataObjectHelper;
use Interprise\Logger\Api\Data\PaymentmethodInterfaceFactory;

class Paymentmethod extends \Magento\Framework\Model\AbstractModel
{

    protected $dataObjectHelper;

    protected $_eventPrefix = 'interprise_payment_methods';
    protected $paymentmethodDataFactory;
    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param PaymentmethodInterfaceFactory $paymentmethodDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Interprise\Logger\Model\ResourceModel\Paymentmethod $resource
     * @param \Interprise\Logger\Model\ResourceModel\Paymentmethod\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        PaymentmethodInterfaceFactory $paymentmethodDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Interprise\Logger\Model\ResourceModel\Paymentmethod $resource,
        \Interprise\Logger\Model\ResourceModel\Paymentmethod\Collection $resourceCollection,
        array $data = []
    ) {
        $this->paymentmethodDataFactory = $paymentmethodDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve paymentmethod model with paymentmethod data
     * @return PaymentmethodInterface
     */
    public function getDataModel()
    {
        $paymentmethodData = $this->getData();
        
        $paymentmethodDataObject = $this->paymentmethodDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $paymentmethodDataObject,
            $paymentmethodData,
            PaymentmethodInterface::class
        );
        
        return $paymentmethodDataObject;
    }
}

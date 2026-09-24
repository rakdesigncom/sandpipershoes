<?php


namespace Interprise\Logger\Model;

use Magento\Framework\Api\DataObjectHelper;
use Interprise\Logger\Api\Data\CustompaymentInterfaceFactory;
use Interprise\Logger\Api\Data\CustompaymentInterface;

class Custompayment extends \Magento\Framework\Model\AbstractModel
{

    protected $dataObjectHelper;

    protected $custompaymentDataFactory;

    protected $_eventPrefix = 'interprise_custom_payment';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param CustompaymentInterfaceFactory $custompaymentDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Interprise\Logger\Model\ResourceModel\Custompayment $resource
     * @param \Interprise\Logger\Model\ResourceModel\Custompayment\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        CustompaymentInterfaceFactory $custompaymentDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Interprise\Logger\Model\ResourceModel\Custompayment $resource,
        \Interprise\Logger\Model\ResourceModel\Custompayment\Collection $resourceCollection,
        array $data = []
    ) {
        $this->custompaymentDataFactory = $custompaymentDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve custompayment model with custompayment data
     * @return CustompaymentInterface
     */
    public function getDataModel()
    {
        $custompaymentData = $this->getData();
        
        $custompaymentDataObject = $this->custompaymentDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $custompaymentDataObject,
            $custompaymentData,
            CustompaymentInterface::class
        );
        
        return $custompaymentDataObject;
    }
}

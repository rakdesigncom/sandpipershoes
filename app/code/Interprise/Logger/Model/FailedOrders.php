<?php


namespace Interprise\Logger\Model;

use Magento\Framework\Api\DataObjectHelper;
use Interprise\Logger\Api\Data\FailedOrdersInterfaceFactory;
use Interprise\Logger\Api\Data\FailedOrdersInterface;

class FailedOrders extends \Magento\Framework\Model\AbstractModel
{

    protected $dataObjectHelper;

    protected $_eventPrefix = 'interprise_logger_failedorders';
    protected $FailedOrdersDataFactory;
    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param CasesInterfaceFactory $caseDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Interprise\Logger\Model\ResourceModel\Cases $resource
     * @param \Interprise\Logger\Model\ResourceModel\Cases\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        FailedOrdersInterfaceFactory $caseDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Interprise\Logger\Model\ResourceModel\FailedOrders $resource,
        \Interprise\Logger\Model\ResourceModel\FailedOrders\Collection $resourceCollection,
        array $data = []
    ) {
        $this->caseDataFactory = $caseDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve case model with case data
     * @return CasesInterface
     */
    public function getDataModel()
    {
        $caseData = $this->getData();

        $caseDataObject = $this->caseDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $caseDataObject,
            $caseData,
            FailedOrdersInterface::class
        );

        return $caseDataObject;
    }
}

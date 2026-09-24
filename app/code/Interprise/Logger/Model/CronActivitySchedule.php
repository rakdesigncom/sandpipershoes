<?php


namespace Interprise\Logger\Model;

use Magento\Framework\Api\DataObjectHelper;
use Interprise\Logger\Api\Data\CronActivityScheduleInterfaceFactory;
use Interprise\Logger\Api\Data\CronActivityScheduleInterface;

class CronActivitySchedule extends \Magento\Framework\Model\AbstractModel
{

    protected $dataObjectHelper;

    protected $_eventPrefix = 'interprise_logger_cronactivityschedule';
    protected $cronlogDataFactory;
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
        CronActivityScheduleInterfaceFactory $caseDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Interprise\Logger\Model\ResourceModel\CronActivitySchedule $resource,
        \Interprise\Logger\Model\ResourceModel\CronActivitySchedule\Collection $resourceCollection,
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
            CronActivityScheduleInterface::class
        );

        return $caseDataObject;
    }
}

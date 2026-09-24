<?php


namespace Interprise\Logger\Model;

use Magento\Framework\Api\DataObjectHelper;
use Interprise\Logger\Api\Data\StatementaccountInterface;
use Interprise\Logger\Api\Data\StatementaccountInterfaceFactory;

class Statementaccount extends \Magento\Framework\Model\AbstractModel
{

    protected $dataObjectHelper;

    protected $_eventPrefix = 'interprise_statement_account';
    protected $statementaccountDataFactory;
    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param StatementaccountInterfaceFactory $statementaccountDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Interprise\Logger\Model\ResourceModel\Statementaccount $resource
     * @param \Interprise\Logger\Model\ResourceModel\Statementaccount\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        StatementaccountInterfaceFactory $statementaccountDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Interprise\Logger\Model\ResourceModel\Statementaccount $resource,
        \Interprise\Logger\Model\ResourceModel\Statementaccount\Collection $resourceCollection,
        array $data = []
    ) {
        $this->statementaccountDataFactory = $statementaccountDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve statementaccount model with statementaccount data
     * @return StatementaccountInterface
     */
    public function getDataModel()
    {
        $statementaccountData = $this->getData();
        
        $statementaccountDataObject = $this->statementaccountDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $statementaccountDataObject,
            $statementaccountData,
            StatementaccountInterface::class
        );
        
        return $statementaccountDataObject;
    }
}

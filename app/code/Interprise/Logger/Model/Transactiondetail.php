<?php


namespace Interprise\Logger\Model;

use Magento\Framework\Api\DataObjectHelper;
use Interprise\Logger\Api\Data\TransactiondetailInterfaceFactory;
use Interprise\Logger\Api\Data\TransactiondetailInterface;

class Transactiondetail extends \Magento\Framework\Model\AbstractModel
{

    protected $dataObjectHelper;

    protected $transactiondetailDataFactory;

    protected $_eventPrefix = 'interprise_transaction_detail';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param TransactiondetailInterfaceFactory $transactiondetailDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Interprise\Logger\Model\ResourceModel\Transactiondetail $resource
     * @param \Interprise\Logger\Model\ResourceModel\Transactiondetail\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        TransactiondetailInterfaceFactory $transactiondetailDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Interprise\Logger\Model\ResourceModel\Transactiondetail $resource,
        \Interprise\Logger\Model\ResourceModel\Transactiondetail\Collection $resourceCollection,
        array $data = []
    ) {
        $this->transactiondetailDataFactory = $transactiondetailDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve transactiondetail model with transactiondetail data
     * @return TransactiondetailInterface
     */
    public function getDataModel()
    {
        $transactiondetailData = $this->getData();
        
        $transactiondetailDataObject = $this->transactiondetailDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $transactiondetailDataObject,
            $transactiondetailData,
            TransactiondetailInterface::class
        );
        
        return $transactiondetailDataObject;
    }
}

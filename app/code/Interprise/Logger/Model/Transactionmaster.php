<?php


namespace Interprise\Logger\Model;

use Magento\Framework\Api\DataObjectHelper;
use Interprise\Logger\Api\Data\TransactionmasterInterfaceFactory;
use Interprise\Logger\Api\Data\TransactionmasterInterface;

class Transactionmaster extends \Magento\Framework\Model\AbstractModel
{

    protected $transactionmasterDataFactory;

    protected $dataObjectHelper;

    protected $_eventPrefix = 'interprise_transaction_master';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param TransactionmasterInterfaceFactory $transactionmasterDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Interprise\Logger\Model\ResourceModel\Transactionmaster $resource
     * @param \Interprise\Logger\Model\ResourceModel\Transactionmaster\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        TransactionmasterInterfaceFactory $transactionmasterDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Interprise\Logger\Model\ResourceModel\Transactionmaster $resource,
        \Interprise\Logger\Model\ResourceModel\Transactionmaster\Collection $resourceCollection,
        array $data = []
    ) {
        $this->transactionmasterDataFactory = $transactionmasterDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve transactionmaster model with transactionmaster data
     * @return TransactionmasterInterface
     */
    public function getDataModel()
    {
        $transactionmasterData = $this->getData();
        
        $transactionmasterDataObject = $this->transactionmasterDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $transactionmasterDataObject,
            $transactionmasterData,
            TransactionmasterInterface::class
        );
        
        return $transactionmasterDataObject;
    }
}

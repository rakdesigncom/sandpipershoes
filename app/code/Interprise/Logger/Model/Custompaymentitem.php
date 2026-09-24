<?php


namespace Interprise\Logger\Model;

use Interprise\Logger\Api\Data\CustompaymentitemInterfaceFactory;
use Interprise\Logger\Api\Data\CustompaymentitemInterface;
use Magento\Framework\Api\DataObjectHelper;

class Custompaymentitem extends \Magento\Framework\Model\AbstractModel
{

    protected $dataObjectHelper;

    protected $_eventPrefix = 'interprise_custom_payment_item';
    protected $custompaymentitemDataFactory;
    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param CustompaymentitemInterfaceFactory $custompaymentitemDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Interprise\Logger\Model\ResourceModel\Custompaymentitem $resource
     * @param \Interprise\Logger\Model\ResourceModel\Custompaymentitem\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        CustompaymentitemInterfaceFactory $custompaymentitemDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Interprise\Logger\Model\ResourceModel\Custompaymentitem $resource,
        \Interprise\Logger\Model\ResourceModel\Custompaymentitem\Collection $resourceCollection,
        array $data = []
    ) {
        $this->custompaymentitemDataFactory = $custompaymentitemDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
    /**
     * Retrieve custompaymentitem model with custompaymentitem data
     * @return CustompaymentitemInterface
     */
    public function getDataModel()
    {
        $custompaymentitemData = $this->getData();
        
        $custompaymentitemDataObject = $this->custompaymentitemDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $custompaymentitemDataObject,
            $custompaymentitemData,
            CustompaymentitemInterface::class
        );
        
        return $custompaymentitemDataObject;
    }
}

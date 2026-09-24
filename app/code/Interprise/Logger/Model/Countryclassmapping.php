<?php


namespace Interprise\Logger\Model;

use Interprise\Logger\Api\Data\CountryclassmappingInterface;
use Interprise\Logger\Api\Data\CountryclassmappingInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class Countryclassmapping extends \Magento\Framework\Model\AbstractModel
{

    protected $countryclassmappingDataFactory;

    protected $dataObjectHelper;

    protected $_eventPrefix = 'interprise_country_class_mapping';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param CountryclassmappingInterfaceFactory $countryclassmappingDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Interprise\Logger\Model\ResourceModel\Countryclassmapping $resource
     * @param \Interprise\Logger\Model\ResourceModel\Countryclassmapping\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        CountryclassmappingInterfaceFactory $countryclassmappingDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Interprise\Logger\Model\ResourceModel\Countryclassmapping $resource,
        \Interprise\Logger\Model\ResourceModel\Countryclassmapping\Collection $resourceCollection,
        array $data = []
    ) {
        $this->countryclassmappingDataFactory = $countryclassmappingDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve countryclassmapping model with countryclassmapping data
     * @return CountryclassmappingInterface
     */
    public function getDataModel()
    {
        $countryclassmappingData = $this->getData();
        
        $countryclassmappingDataObject = $this->countryclassmappingDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $countryclassmappingDataObject,
            $countryclassmappingData,
            CountryclassmappingInterface::class
        );
        
        return $countryclassmappingDataObject;
    }
}

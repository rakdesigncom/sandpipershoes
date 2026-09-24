<?php


namespace Interprise\Logger\Model;

use Magento\Framework\Api\DataObjectHelper;
use Interprise\Logger\Api\Data\InstallwizardInterface;
use Interprise\Logger\Api\Data\InstallwizardInterfaceFactory;

class Installwizard extends \Magento\Framework\Model\AbstractModel
{

    protected $dataObjectHelper;

    protected $installwizardDataFactory;

    protected $_eventPrefix = 'interprise_install_wizard';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param InstallwizardInterfaceFactory $installwizardDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Interprise\Logger\Model\ResourceModel\Installwizard $resource
     * @param \Interprise\Logger\Model\ResourceModel\Installwizard\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        InstallwizardInterfaceFactory $installwizardDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Interprise\Logger\Model\ResourceModel\Installwizard $resource,
        \Interprise\Logger\Model\ResourceModel\Installwizard\Collection $resourceCollection,
        array $data = []
    ) {
        $this->installwizardDataFactory = $installwizardDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve installwizard model with installwizard data
     * @return InstallwizardInterface
     */
    public function getDataModel()
    {
        $installwizardData = $this->getData();
        
        $installwizardDataObject = $this->installwizardDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $installwizardDataObject,
            $installwizardData,
            InstallwizardInterface::class
        );
        
        return $installwizardDataObject;
    }
}

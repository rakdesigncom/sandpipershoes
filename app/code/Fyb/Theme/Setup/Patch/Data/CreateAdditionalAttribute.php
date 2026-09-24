<?php

namespace Fyb\Theme\Setup\Patch\Data;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class CreateAdditionalAttribute implements DataPatchInterface
{
    /**
     * @var CustomerSetupFactory
     */
    protected $customerSetupFactory;

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    private $csvReader;
    private $attributeRepository;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeSetFactory $attributeSetFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory,
        \Magento\Framework\File\Csv $csvReader,
        \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->csvReader = $csvReader;
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * Get dependencies
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * Add eav attributes
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();
        /** @var $attributeSet AttributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $customerSetup->addAttribute(Customer::ENTITY, 'interprise_pricing_band', [
            'type' => 'varchar',
            'label' => 'Interprise Price Band',
            'input' => 'text',
            'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
            'required' => false,
            'visible' => true,
            'position' => 700,
            'system' => false,
            'backend' => ''
        ]);
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'interprise_pricing_band')
            ->addData(['used_in_forms' => [
                'adminhtml_customer',
                'adminhtml_checkout',
                'customer_account_create',
                'customer_account_edit'
            ]]);
        $attribute->save();

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Get Aliases
     */
    public function getAliases()
    {
        return [];
    }
}

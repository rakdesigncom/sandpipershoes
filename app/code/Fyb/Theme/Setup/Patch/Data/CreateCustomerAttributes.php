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

class CreateCustomerAttributes implements DataPatchInterface
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

    protected function getAttributes()
    {
        $obj = ObjectManager::getInstance();
        $rootPath = $obj->get(\Magento\Framework\Filesystem\DirectoryList::class)->getRoot();
        $rootPath .= '/scripts/csv/customer_attributes.csv';
        $attributes = $this->csvReader->getData($rootPath);

        $header = $attributes[0];
        unset($attributes[0]);
        $data = [];
        foreach ($attributes as $keyAttr => $attribute) {
            foreach ($attribute as $key => $attributeData) {
                $data[$keyAttr][$header[$key]] = $attributeData;
            }
        }

        $attributeCode = [
            'with_vat_shipto_code',
            'vat_exempt_shipto_code',
            'vatexemptexpirydate_c',
            'interprise_previous_classcode',
        ];
        $attributesToCreate = [];
        foreach ($data as $row) {
            if ($row['backend_type'] == 'static') {
                continue;
            }

            if (!in_array($row['attribute_code'], $attributeCode)) {
                continue;
            }

            $attributesToCreate[] = $row;
        }

        return $attributesToCreate;
    }

    /**
     * Add eav attributes
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $attributes = $this->getAttributes();

        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();
        /** @var $attributeSet AttributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        foreach ($attributes as $attributeData) {
                $customerSetup->addAttribute(Customer::ENTITY, $attributeData['attribute_code'], [
                    'type' => $attributeData['backend_type'],
                    'label' => $attributeData['frontend_label'],
                    'input' => $attributeData['frontend_input'],
                    'source' => '',
                    'required' => false,
                    'visible' => true,
                    'position' => 700,
                    'system' => false,
                    'backend' => ''
                ]);
                $attribute = $customerSetup->getEavConfig()->getAttribute('customer', $attributeData['attribute_code'])
                    ->addData(['used_in_forms' => [
                        'adminhtml_customer',
                        'adminhtml_checkout',
                        'customer_account_create',
                        'customer_account_edit'
                    ]]);
                $attribute->save();
        }

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

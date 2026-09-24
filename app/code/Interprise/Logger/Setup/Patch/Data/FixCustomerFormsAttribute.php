<?php

namespace Interprise\Logger\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\Eav\Model\Entity\Attribute\SetFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

class FixCustomerFormsAttribute implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var CustomerSetup
     */
    private $customerSetupFactory;

    /**
     * @var SetFactory
     */
    private $attributeSetFactory;

    /**
     * Constructor
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CustomerSetupFactory $customerSetupFactory
     * @param SetFactory $attributeSetFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CustomerSetupFactory $customerSetupFactory,
        SetFactory $attributeSetFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $customerEntity = $customerSetup->getEavConfig()->getEntityType(Customer::ENTITY);
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        /** @var $attributeSet Set */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $attributes = [
            'vatexemptreason_c',
            'vatexemptcustomer_c',
            'interprise_pricing_band',
            'with_vat_shipto_code',
            'vat_exempt_shipto_code',
            'vatexemptexpirydate_c',
            'interprise_previous_classcode',
            'interprise_iswebaccess',
            'interprise_isallowbackorder',
            'interprise_iscredithold',
            'interprise_pricingpercent',
            'interprise_credit',
            'interprise_creditlimit',
            'interprise_currencycode',
            'interprise_classcode',
            'interprise_apicreated',
            'interprise_discount',
            'interprise_paymentlink',
            'interprise_businesstype',
            'interprise_customertypecode',
            'interprise_defaultshiptocode',
            'interprise_taxcode',
            'interprise_pricinglevel',
            'interprise_pricingmethod',
            'interprise_defaultprice',
            'interprise_ptcode',
            'interprise_ptgroup',
            'interprise_shippingmethod',
            'interprise_smethod_group',
            'interprise_customer_code',

        ];

        foreach ($attributes as $attributeCode) {
            $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, $attributeCode);
            $attribute->addData([
                'used_in_forms' => ['adminhtml_customer'],
            ]);
            $attribute->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
            ]);
            $attribute->save();
        }

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public function revert()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $customerSetup->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, 'test');

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}

<?php


namespace Interprise\Logger\Setup;

use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Customer\Model\Customer;
use Magento\Sales\Model\Order;
use Magento\Eav\Setup\EavSetup;
use Interprise\Logger\Model\CronMaster;
use Interprise\Logger\Model\CronMasterFactory;

class InstallData implements InstallDataInterface
{

    protected $salesSetupFactory;
    private $customerSetupFactory;
    private $eavSetupFactory;
    private $cronfactory;

    /**
     * Constructor
     *
     * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        EavSetupFactory $eavSetupFactory,
        CronMasterFactory $cronFactory,
        \Magento\Sales\Setup\SalesSetupFactory $salesSetupFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->cronfactory = $cronFactory;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'interprise_customer_code', [
            'type' => 'varchar',
            'label' => 'Interprise Customer Code',
            'input' => 'text',
            'source' => '',
            'required' => false,
            'visible' => true,
            'position' => 333,
            'system' => false,
            'backend' => ''
        ]);
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'interprise_customer_code')
            ->addData(['used_in_forms' => [
                'adminhtml_customer',
                'adminhtml_checkout',
                'customer_account_create',
                'customer_account_edit'
            ]]);
        $attribute->save();

        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'interprise_smethod_group', [
            'type' => 'varchar',
            'label' => 'Interprise shippingMethodGroup',
            'input' => 'text',
            'source' => '',
            'required' => false,
            'visible' => true,
            'position' => 400,
            'system' => false,
            'backend' => ''
        ]);
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'interprise_smethod_group')
            ->addData(['used_in_forms' => [
                'adminhtml_customer',
                'adminhtml_checkout',
                'customer_account_create',
                'customer_account_edit'
            ]]);
        $attribute->save();
        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'interprise_shippingmethod', [
            'type' => 'varchar',
            'label' => 'Interprise shippingMethod',
            'input' => 'text',
            'source' => '',
            'required' => false,
            'visible' => true,
            'position' => 500,
            'system' => false,
            'backend' => ''
        ]);
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'interprise_shippingmethod')
            ->addData(['used_in_forms' => [
                'adminhtml_customer',
                'adminhtml_checkout',
                'customer_account_create',
                'customer_account_edit'
            ]]);
        $attribute->save();
        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'interprise_ptgroup', [
            'type' => 'varchar',
            'label' => 'Interprise paymentTermGroup',
            'input' => 'text',
            'source' => '',
            'required' => false,
            'visible' => true,
            'position' => 600,
            'system' => false,
            'backend' => ''
        ]);
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'interprise_ptgroup')
            ->addData(['used_in_forms' => [
                'adminhtml_customer',
                'adminhtml_checkout',
                'customer_account_create',
                'customer_account_edit'
            ]]);
        $attribute->save();
        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'interprise_ptcode', [
            'type' => 'varchar',
            'label' => 'Interprise paymentTermCode',
            'input' => 'text',
            'source' => '',
            'required' => false,
            'visible' => true,
            'position' => 700,
            'system' => false,
            'backend' => ''
        ]);
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'interprise_ptcode')
            ->addData(['used_in_forms' => [
                'adminhtml_customer',
                'adminhtml_checkout',
                'customer_account_create',
                'customer_account_edit'
            ]]);
        $attribute->save();
        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'interprise_defaultprice', [
            'type' => 'varchar',
            'label' => 'Interprise defaultPrice',
            'input' => 'text',
            'source' => '',
            'required' => false,
            'visible' => true,
            'position' => 800,
            'system' => false,
            'backend' => ''
        ]);
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'interprise_defaultprice')
            ->addData(['used_in_forms' => [
                'adminhtml_customer',
                'adminhtml_checkout',
                'customer_account_create',
                'customer_account_edit'
            ]]);
        $attribute->save();
        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'interprise_pricingmethod', [
            'type' => 'varchar',
            'label' => 'Interprise pricingMethod',
            'input' => 'text',
            'source' => '',
            'required' => false,
            'visible' => true,
            'position' => 800,
            'system' => false,
            'backend' => ''
        ]);
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'interprise_pricingmethod')
            ->addData(['used_in_forms' => [
                'adminhtml_customer',
                'adminhtml_checkout',
                'customer_account_create',
                'customer_account_edit'
            ]]);
        $attribute->save();
        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'interprise_pricinglevel', [
            'type' => 'varchar',
            'label' => 'Interprise pricingLevel',
            'input' => 'text',
            'source' => '',
            'required' => false,
            'visible' => true,
            'position' => 800,
            'system' => false,
            'backend' => ''
        ]);
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'interprise_pricinglevel')
            ->addData(['used_in_forms' => [
                'adminhtml_customer',
                'adminhtml_checkout',
                'customer_account_create',
                'customer_account_edit'
            ]]);
        $attribute->save();
        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'interprise_taxcode', [
            'type' => 'varchar',
            'label' => 'Interprise taxCode',
            'input' => 'text',
            'source' => '',
            'required' => false,
            'visible' => true,
            'position' => 800,
            'system' => false,
            'backend' => ''
        ]);
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'interprise_taxcode')
            ->addData(['used_in_forms' => [
                'adminhtml_customer',
                'adminhtml_checkout',
                'customer_account_create',
                'customer_account_edit'
            ]]);
        $attribute->save();
        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'interprise_defaultshiptocode', [
            'type' => 'varchar',
            'label' => 'Interprise defaultShipToCode',
            'input' => 'text',
            'source' => '',
            'required' => false,
            'visible' => true,
            'position' => 800,
            'system' => false,
            'backend' => ''
        ]);
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'interprise_defaultshiptocode')
            ->addData(['used_in_forms' => [
                'adminhtml_customer',
                'adminhtml_checkout',
                'customer_account_create',
                'customer_account_edit'
            ]]);
        $attribute->save();
        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'interprise_customertypecode', [
            'type' => 'varchar',
            'label' => 'Interprise customerTypeCode',
            'input' => 'text',
            'source' => '',
            'required' => false,
            'visible' => true,
            'position' => 800,
            'system' => false,
            'backend' => ''
        ]);
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'interprise_customertypecode')
            ->addData(['used_in_forms' => [
                'adminhtml_customer',
                'adminhtml_checkout',
                'customer_account_create',
                'customer_account_edit'
            ]]);
        $attribute->save();
        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'interprise_businesstype', [
            'type' => 'varchar',
            'label' => 'Interprise businessType',
            'input' => 'text',
            'source' => '',
            'required' => false,
            'visible' => true,
            'position' => 800,
            'system' => false,
            'backend' => ''
        ]);
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'interprise_businesstype')
            ->addData(['used_in_forms' => [
                'adminhtml_customer',
                'adminhtml_checkout',
                'customer_account_create',
                'customer_account_edit'
            ]]);
        $attribute->save();
        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'interprise_paymentlink', [
            'type' => 'varchar',
            'label' => 'Interprise PaymentLink ',
            'input' => 'text',
            'source' => '',
            'required' => false,
            'visible' => true,
            'position' => 800,
            'system' => false,
            'backend' => ''
        ]);
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'interprise_paymentlink')
            ->addData(['used_in_forms' => [
                'adminhtml_customer',
                'adminhtml_checkout',
                'customer_account_create',
                'customer_account_edit'
            ]]);
        $attribute->save();
        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'interprise_discount', [
            'type' => 'varchar',
            'label' => 'Interprise Discount ',
            'input' => 'text',
            'source' => '',
            'required' => false,
            'visible' => true,
            'position' => 800,
            'system' => false,
            'backend' => ''
        ]);
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'interprise_discount')
            ->addData(['used_in_forms' => [
                'adminhtml_customer',
                'adminhtml_checkout',
                'customer_account_create',
                'customer_account_edit'
            ]]);
        $attribute->save();
        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'interprise_apicreated', [
            'type' => 'varchar',
            'label' => 'Interprise API Created ',
            'input' => 'text',
            'source' => '',
            'required' => false,
            'visible' => true,
            'position' => 800,
            'system' => false,
            'backend' => ''
        ]);
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'interprise_apicreated')
            ->addData(['used_in_forms' => [
                'adminhtml_customer',
                'adminhtml_checkout'
            ]]);
        $attribute->save();

        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'interprise_classcode', [
            'type' => 'varchar',
            'label' => 'Interprise classCode',
            'input' => 'text',
            'source' => '',
            'required' => false,
            'visible' => true,
            'position' => 800,
            'system' => false,
            'backend' => ''
        ]);
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'interprise_classcode')
            ->addData(['used_in_forms' => [
                'adminhtml_customer',
                'adminhtml_checkout'
            ]]);
        $attribute->save();

        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'interprise_currencycode', [
            'type' => 'varchar',
            'label' => 'Interprise currencyCode',
            'input' => 'text',
            'source' => '',
            'required' => false,
            'visible' => true,
            'position' => 800,
            'system' => false,
            'backend' => ''
        ]);
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'interprise_currencycode')
            ->addData(['used_in_forms' => [
                'adminhtml_customer',
                'adminhtml_checkout'
            ]]);
        $attribute->save();
        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'interprise_creditlimit', [
            'type' => 'varchar',
            'label' => 'Interprise creditLimit',
            'input' => 'text',
            'source' => '',
            'required' => false,
            'visible' => true,
            'position' => 800,
            'system' => false,
            'backend' => ''
        ]);
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'interprise_creditlimit')
            ->addData(['used_in_forms' => [
                'adminhtml_customer',
                'adminhtml_checkout'
            ]]);
        $attribute->save();
        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'interprise_credit', [
            'type' => 'varchar',
            'label' => 'Interprise credit',
            'input' => 'text',
            'source' => '',
            'required' => false,
            'visible' => true,
            'position' => 800,
            'system' => false,
            'backend' => ''
        ]);
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'interprise_credit')
            ->addData(['used_in_forms' => [
                'adminhtml_customer',
                'adminhtml_checkout'
            ]]);
        $attribute->save();
        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'interprise_pricingpercent', [
            'type' => 'varchar',
            'label' => 'Interprise pricingpercent',
            'input' => 'text',
            'source' => '',
            'required' => false,
            'visible' => true,
            'position' => 800,
            'system' => false,
            'backend' => ''
        ]);
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'interprise_pricingpercent')
            ->addData(['used_in_forms' => [
                'adminhtml_customer',
                'adminhtml_checkout'
            ]]);
        $attribute->save();
        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'interprise_iscredithold', [
            'type' => 'varchar',
            'label' => 'Interprise isCreditHold',
            'input' => 'text',
            'source' => '',
            'required' => false,
            'visible' => true,
            'position' => 800,
            'system' => false,
            'backend' => ''
        ]);
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'interprise_iscredithold')
            ->addData(['used_in_forms' => [
                'adminhtml_customer',
                'adminhtml_checkout'
            ]]);
        $attribute->save();
        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'interprise_isallowbackorder', [
            'type' => 'varchar',
            'label' => 'Interprise isAllowBackOrder',
            'input' => 'text',
            'source' => '',
            'required' => false,
            'visible' => true,
            'position' => 800,
            'system' => false,
            'backend' => ''
        ]);
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'interprise_isallowbackorder')
            ->addData(['used_in_forms' => [
                'adminhtml_customer',
                'adminhtml_checkout'
            ]]);
        $attribute->save();
        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'interprise_iswebaccess', [
            'type' => 'varchar',
            'label' => 'Interprise isWebAccess',
            'input' => 'text',
            'source' => '',
            'required' => false,
            'visible' => true,
            'position' => 800,
            'system' => false,
            'backend' => ''
        ]);
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'interprise_iswebaccess')
            ->addData(['used_in_forms' => [
                'adminhtml_customer',
                'adminhtml_checkout'
            ]]);
        $attribute->save();
        $customerSetup->addAttribute(
            \Magento\Customer\Model\Indexer\Address\AttributeProvider::ENTITY,
            'interprise_shiptocode',
            [
            'label' => 'Shipto Code',
            'input' => 'text',
            'type' => 'varchar',
            'source' => '',
            'required' => false,
            'position' => 600,
            'visible' => true,
            'system' => false,
            'is_used_in_grid' => false,
            'is_visible_in_grid' => false,
            'is_filterable_in_grid' => false,
            'is_searchable_in_grid' => false,
            'backend' => ''
            ]
        );
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'interprise_shiptocode')
            ->addData(['used_in_forms' => [
                'adminhtml_customer_address'
            ]]);
        $attribute->save();

        $customerSetup->addAttribute(
            \Magento\Customer\Model\Indexer\Address\AttributeProvider::ENTITY,
            'interprise_shippingmethodgroup',
            [
            'label' => 'IS shippingMethodGroup',
            'input' => 'text',
            'type' => 'varchar',
            'source' => '',
            'required' => false,
            'position' => 600,
            'visible' => true,
            'system' => false,
            'is_used_in_grid' => false,
            'is_visible_in_grid' => false,
            'is_filterable_in_grid' => false,
            'is_searchable_in_grid' => false,
            'backend' => ''
            ]
        );
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'interprise_shippingmethodgroup')
            ->addData(['used_in_forms' => [
                'adminhtml_customer_address'
            ]]);
        $attribute->save();
        $customerSetup->addAttribute(
            \Magento\Customer\Model\Indexer\Address\AttributeProvider::ENTITY,
            'interprise_shippingmethod',
            [
            'label' => 'IS shippingMethod',
            'input' => 'text',
            'type' => 'varchar',
            'source' => '',
            'required' => false,
            'position' => 600,
            'visible' => true,
            'system' => false,
            'is_used_in_grid' => false,
            'is_visible_in_grid' => false,
            'is_filterable_in_grid' => false,
            'is_searchable_in_grid' => false,
            'backend' => ''
            ]
        );
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'interprise_shippingmethod')
            ->addData(['used_in_forms' => [
                'adminhtml_customer_address'
            ]]);
        $attribute->save();
        $customerSetup->addAttribute(
            \Magento\Customer\Model\Indexer\Address\AttributeProvider::ENTITY,
            'interprise_freighttax',
            [
            'label' => 'IS freightTax',
            'input' => 'text',
            'type' => 'varchar',
            'source' => '',
            'required' => false,
            'position' => 600,
            'visible' => true,
            'system' => false,
            'is_used_in_grid' => false,
            'is_visible_in_grid' => false,
            'is_filterable_in_grid' => false,
            'is_searchable_in_grid' => false,
            'backend' => ''
            ]
        );
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'interprise_freighttax')
            ->addData(['used_in_forms' => [
                'adminhtml_customer_address'
            ]]);
        $attribute->save();
        $customerSetup->addAttribute(
            \Magento\Customer\Model\Indexer\Address\AttributeProvider::ENTITY,
            'interprise_apicreatedaddress',
            [
            'label' => 'Interprise Api Created',
            'input' => 'text',
            'type' => 'varchar',
            'source' => '',
            'required' => false,
            'position' => 600,
            'visible' => true,
            'system' => false,
            'is_used_in_grid' => false,
            'is_visible_in_grid' => false,
            'is_filterable_in_grid' => false,
            'is_searchable_in_grid' => false,
            'backend' => ''
            ]
        );
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'interprise_apicreatedaddress')
            ->addData(['used_in_forms' => [
                'adminhtml_customer_address'
            ]]);
        $attribute->save();

        /* Start create new attributes of customer address */
        /*$eavSetup_address = $this->_eavSetupFactory->create(['setup' => $setup]);
        $eavSetup_address->addAttribute('customer_address', 'interprise_shipto_code', array(
            'type' => 'varchar',
            'input' => 'text',
            'label' => 'Shipto Code',
            'source'=>'',
            //'global' => 1,
            'visible' => true,
            'required' => false,
            'user_defined' => 1,
            'system'=>false,
             'backend' => ''
            //'visible_on_front' => false,
        ));
        $eavSetup_address->getEavConfig()->getAttribute('customer_address','interprise_shipto_code')
               ->setUsedInForms(array('adminhtml_customer_address','customer_address_edit','customer_register_address'))
               ->save();

        /* End create new attributes of customer address */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'interprise_category_code',
            [
                'type' => 'varchar',
                'label' => 'Interprise Category Code',
                'input' => 'text',
                'sort_order' => 333,
                'source' => '',
                'global' => 1,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => null,
                'group' => 'General Information',
                'backend' => ''
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'interprise_item_code',
            [
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Interprise Item Code',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => 1,
                'visible' => true,
                'required' => true,
                'user_defined' => true,
                'default' => null,
                'searchable' => true,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => true,
                'apply_to' => 'simple,grouped,bundle,configurable,virtual',
                'system' => 1,
                'group' => 'Interprise',
                'option' => ['values' => [""]]
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'interprise_item_type',
            [
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Interprise itemType',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => 1,
                'visible' => true,
                'required' => true,
                'user_defined' => true,
                'default' => null,
                'searchable' => true,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => 'simple,grouped,bundle,configurable,virtual',
                'system' => 1,
                'group' => 'Interprise',
                'option' => ['values' => [""]]
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'is_unitmeasurecode',
            [
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Interprise Unitmeasurecode',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => 1,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => null,
                'searchable' => true,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => 'simple,grouped,bundle,configurable,virtual',
                'system' => 1,
                'group' => 'Interprise',
                'option' => ['values' => [""]]
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'manufacturercode',
            [
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Interprise Manufacturercode',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => 1,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => null,
                'searchable' => true,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => 'simple,grouped,bundle,configurable,virtual',
                'system' => 1,
                'group' => 'Interprise',
                'option' => ['values' => [""]]
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'upccode',
            [
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Interprise UPC Code',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => 1,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => null,
                'searchable' => true,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => 'simple,grouped,bundle,configurable,virtual',
                'system' => 1,
                'group' => 'Interprise',
                'option' => ['values' => [""]]
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'is_retailprice',
            [
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Interprise Retail Price',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => 1,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => null,
                'searchable' => true,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => 'simple,grouped,bundle,configurable,virtual',
                'system' => 1,
                'group' => 'Interprise',
                'option' => ['values' => [""]]
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'is_wholesaleprice',
            [
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Interprise Wholesale Price',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => 1,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => null,
                'searchable' => true,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => 'simple,grouped,bundle,configurable,virtual',
                'system' => 1,
                'group' => 'Interprise',
                'option' => ['values' => [""]]
            ]
        );
        $setup->startSetup();

        $salesSetup = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $setup]);
        $salesSetup->addAttribute(Order::ENTITY, 'is_paymenttcode', [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length' => 255,
            'visible' => false,
            'nullable' => true
        ]);

        $setup->getConnection()->insert(
            $setup->getTable('interprise_install_wizard'),
            [
                'item_name' => 'Countries and Currencies',
                'status' => 'pending',
                'action' => 'Process',
                'total_records' => '0',
                'function_name' => 'import_country',
                'sync_done' => '0',
                'sort_order' => '10'
            ]
        );
        $setup->getConnection()->insert(
            $setup->getTable('interprise_install_wizard'),
            [
                'item_name' => 'Payment Methods',
                'status' => 'pending',
                'action' => 'Process',
                'total_records' => '0',
                'function_name' => 'payment_methods',
                'sync_done' => '0',
                'sort_order' => '20'
            ]
        );
        $setup->getConnection()->insert(
            $setup->getTable('interprise_install_wizard'),
            [
                'item_name' => 'Shipping Methods',
                'status' => 'pending',
                'action' => 'Process',
                'total_records' => '0',
                'function_name' => 'shipping_methods',
                'sync_done' => '0',
                'sort_order' => '30'
            ]
        );
        $setup->getConnection()->insert(
            $setup->getTable('interprise_install_wizard'),
            [
                'item_name' => 'Category',
                'status' => 'pending',
                'action' => 'Process',
                'total_records' => '0',
                'function_name' => 'category',
                'sync_done' => '0',
                'sort_order' => '40'
            ]
        );
        $setup->getConnection()->insert(
            $setup->getTable('interprise_install_wizard'),
            [
                'item_name' => 'Stock Items',
                'status' => 'pending',
                'action' => 'Process',
                'total_records' => '0',
                'function_name' => 'stock_items',
                'sync_done' => '0',
                'sort_order' => '50'
            ]
        );
        $setup->getConnection()->insert(
            $setup->getTable('interprise_install_wizard'),
            [
                'item_name' => 'Kit Items',
                'status' => 'pending',
                'action' => 'Process',
                'total_records' => '0',
                'function_name' => 'kit_items',
                'sync_done' => '0',
                'sort_order' => '60'
            ]
        );
        $setup->getConnection()->insert(
            $setup->getTable('interprise_install_wizard'),
            [
                'item_name' => 'Matrix Group',
                'status' => 'pending',
                'action' => 'Process',
                'total_records' => '0',
                'function_name' => 'matrix_item',
                'sync_done' => '0',
                'sort_order' => '70'
            ]
        );
        $setup->getConnection()->insert(
            $setup->getTable('interprise_install_wizard'),
            [
                'item_name' => 'Customers',
                'status' => 'pending',
                'action' => 'Process',
                'total_records' => '0',
                'function_name' => 'customers',
                'sync_done' => '0',
                'sort_order' => '80'
            ]
        );
        $setup->getConnection()->insert(
            $setup->getTable('interprise_install_wizard'),
            [
                'item_name' => 'Orders,Quotes & Back Order',
                'status' => 'pending',
                'action' => 'Process',
                'total_records' => '0',
                'function_name' => 'so_order',
                'sync_done' => '0',
                'sort_order' => '90'
            ]
        );
        $setup->getConnection()->insert(
            $setup->getTable('interprise_install_wizard'),
            [
                'item_name' => 'Invoices,Crma & Credit Notes',
                'status' => 'pending',
                'action' => 'Process',
                'total_records' => '0',
                'function_name' => 'invoices_orders',
                'sync_done' => '0',
                'sort_order' => '100'
            ]
        );
        $setup->getConnection()->insert(
            $setup->getTable('interprise_logger_cronmaster'),
            [
                'master_id' => '1',
                'cron_name' => 'ITEM GET',
                'cron_action' => 'GET',
                'cron_function' => 'inventoryitem',
                'cron_changelog_endpoint' => 'inventoryitem',
                'cron_frequency' => '120',
                'cron_status' => '0',
                'cron_active' => '0',
                'cron_from_date' => '2017-01-01'
            ]
        );
        $setup->getConnection()->insert(
            $setup->getTable('interprise_logger_cronmaster'),
            [
                'master_id' => '2',
                'cron_name' => 'CUSTOMER GET',
                'cron_action' => 'GET',
                'cron_function' => 'getCustomer',
                'cron_changelog_endpoint' => 'customer',
                'cron_frequency' => '120',
                'cron_status' => '0',
                'cron_active' => '0',
                'cron_from_date' => '2017-01-01'
            ]
        );
        $setup->getConnection()->insert(
            $setup->getTable('interprise_logger_cronmaster'),
            [
                'master_id' => '3',
                'cron_name' => 'Customer Sales Order GET',
                'cron_action' => 'GET',
                'cron_function' => 'CustomerSalesOrder',
                'cron_changelog_endpoint' => 'CustomerSalesOrder',
                'cron_frequency' => '120',
                'cron_status' => '0',
                'cron_active' => '0',
                'cron_from_date' => '2017-01-01'
            ]
        );
        $setup->getConnection()->insert(
            $setup->getTable('interprise_logger_cronmaster'),
            [
                'master_id' => '4',
                'cron_name' => 'Push Sales Order POST',
                'cron_action' => 'POST',
                'cron_function' => 'pushSalesOrder',
                'cron_changelog_endpoint' => 'pushSalesOrder',
                'cron_frequency' => '120',
                'cron_status' => '0',
                'cron_active' => '0',
                'cron_from_date' => '2017-01-01'
            ]
        );
        $setup->getConnection()->insert(
            $setup->getTable('interprise_logger_cronmaster'),
            [
                'master_id' => '5',
                'cron_name' => 'Inventory Item Pricing Detail Retail GET',
                'cron_action' => 'GET',
                'cron_function' => 'InventoryItemPricingDetailRetail',
                'cron_changelog_endpoint' => 'InventoryItemPricingDetailRetail',
                'cron_frequency' => '120',
                'cron_status' => '0',
                'cron_active' => '0',
                'cron_from_date' => '2017-01-01'
            ]
        );
        $setup->getConnection()->insert(
            $setup->getTable('interprise_logger_cronmaster'),
            [
                'master_id' => '7',
                'cron_name' => 'InventoryStockTotal GET',
                'cron_action' => 'GET',
                'cron_function' => 'InventoryStockTotal',
                'cron_changelog_endpoint' => 'InventoryStockTotal',
                'cron_frequency' => '120',
                'cron_status' => '0',
                'cron_active' => '0',
                'cron_from_date' => '2017-01-01'
            ]
        );
        $setup->getConnection()->insert(
            $setup->getTable('interprise_logger_cronmaster'),
            [
                'master_id' => '8',
                'cron_name' => 'CustomerInvoice GET',
                'cron_action' => 'GET',
                'cron_function' => 'CustomerInvoice',
                'cron_changelog_endpoint' => 'CustomerInvoice',
                'cron_frequency' => '120',
                'cron_status' => '0',
                'cron_active' => '0',
                'cron_from_date' => '2017-01-01'
            ]
        );
        $setup->getConnection()->insert(
            $setup->getTable('interprise_logger_cronmaster'),
            [
                'master_id' => '11',
                'cron_name' => 'CRMActivity GET',
                'cron_action' => 'GET',
                'cron_function' => 'CRMActivity',
                'cron_changelog_endpoint' => 'CRMActivity',
                'cron_frequency' => '120',
                'cron_status' => '0',
                'cron_active' => '0',
                'cron_from_date' => '2017-01-01'
            ]
        );
        $setup->getConnection()->insert(
            $setup->getTable('interprise_logger_cronmaster'),
            [
                'master_id' => '12',
                'cron_name' => 'InventoryMatrixGroup GET',
                'cron_action' => 'GET',
                'cron_function' => 'InventoryMatrixGroup',
                'cron_changelog_endpoint' => 'InventoryMatrixGroup',
                'cron_frequency' => '120',
                'cron_status' => '0',
                'cron_active' => '0',
                'cron_from_date' => '2017-01-01'
            ]
        );
        $setup->getConnection()->insert(
            $setup->getTable('interprise_logger_cronmaster'),
            [
                'master_id' => '13',
                'cron_name' => 'CustomerSalesOrderWorkflow',
                'cron_action' => 'GET',
                'cron_function' => 'CRMActivity',
                'cron_changelog_endpoint' => 'CRMActivity',
                'cron_frequency' => '120',
                'cron_status' => '0',
                'cron_active' => '0',
                'cron_from_date' => '2017-01-01'
            ]
        );
        $setup->getConnection()->insert(
            $setup->getTable('interprise_logger_cronmaster'),
            [
                'master_id' => '16',
                'cron_name' => 'Inventory Kit GET',
                'cron_action' => 'GET',
                'cron_function' => 'InventoryKit',
                'cron_changelog_endpoint' => 'InventoryKit',
                'cron_frequency' => '120',
                'cron_status' => '0',
                'cron_active' => '0',
                'cron_from_date' => '2017-01-01'
            ]
        );
        $setup->getConnection()->insert(
            $setup->getTable('interprise_logger_cronmaster'),
            [
                'master_id' => '17',
                'cron_name' => 'Push crm',
                'cron_action' => 'POST',
                'cron_function' => 'Pushcrm',
                'cron_changelog_endpoint' => 'Pushcrm',
                'cron_frequency' => '120',
                'cron_status' => '0',
                'cron_active' => '0',
                'cron_from_date' => '2017-01-01'
            ]
        );
        $setup->getConnection()->insert(
            $setup->getTable('interprise_logger_cronmaster'),
            [
                'master_id' => '18',
                'cron_name' => 'Customer Special Price GET',
                'cron_action' => 'GET',
                'cron_function' => 'CustomerSpecialPrice',
                'cron_changelog_endpoint' => 'CustomerSpecialPrice',
                'cron_frequency' => '120',
                'cron_status' => '0',
                'cron_active' => '0',
                'cron_from_date' => '2017-01-01'
            ]
        );
        $setup->getConnection()->insert(
            $setup->getTable('interprise_logger_cronmaster'),
            [
                'master_id' => '19',
                'cron_name' => 'Customer ShipTo GET',
                'cron_action' => 'GET',
                'cron_function' => 'CustomerShipTo',
                'cron_changelog_endpoint' => 'CustomerShipTo',
                'cron_frequency' => '120',
                'cron_status' => '0',
                'cron_active' => '0',
                'cron_from_date' => '2017-01-01'
            ]
        );
        $setup->getConnection()->insert(
            $setup->getTable('interprise_logger_cronmaster'),
            [
                'master_id' => '20',
                'cron_name' => 'Customer Sales Order Detail GET',
                'cron_action' => 'GET',
                'cron_function' => 'CustomerSalesOrderDetail',
                'cron_changelog_endpoint' => 'CustomerSalesOrderDetail',
                'cron_frequency' => '120',
                'cron_status' => '0',
                'cron_active' => '0',
                'cron_from_date' => '2017-01-01'
            ]
        );
        $setup->getConnection()->insert(
            $setup->getTable('interprise_logger_cronmaster'),
            [
                'master_id' => '21',
                'cron_name' => 'Customer Address Push',
                'cron_action' => 'POST',
                'cron_function' => 'CustomerAddressPush',
                'cron_changelog_endpoint' => 'CustomerAddressPush',
                'cron_frequency' => '120',
                'cron_status' => '0',
                'cron_active' => '0',
                'cron_from_date' => '2017-01-01'
            ]
        );
        $setup->getConnection()->insert(
            $setup->getTable('interprise_logger_cronmaster'),
            [
                'master_id' => '22',
                'cron_name' => 'Inventory Pricing Level',
                'cron_action' => 'GET',
                'cron_function' => 'InventoryPricingLevel',
                'cron_changelog_endpoint' => 'InventoryPricingLevel',
                'cron_frequency' => '120',
                'cron_status' => '0',
                'cron_active' => '0',
                'cron_from_date' => '2017-01-01'
            ]
        );
        $setup->getConnection()->insert(
            $setup->getTable('interprise_logger_cronmaster'),
            [
                'master_id' => '26',
                'cron_name' => 'PUSH Customer',
                'cron_action' => 'PUT',
                'cron_function' => 'PushCustomer',
                'cron_changelog_endpoint' => 'PushCustomer',
                'cron_frequency' => '120',
                'cron_status' => '0',
                'cron_active' => '0',
                'cron_from_date' => '2017-01-01'
            ]
        );
        $setup->getConnection()->insert(
            $setup->getTable('interprise_logger_cronmaster'),
            [
                'master_id' => '28',
                'cron_name' => 'InventoryKitPricingDetail',
                'cron_action' => 'GET',
                'cron_function' => 'InventoryKitPricingDetail',
                'cron_changelog_endpoint' => 'InventoryKitPricingDetail',
                'cron_frequency' => '120',
                'cron_status' => '0',
                'cron_active' => '0',
                'cron_from_date' => '2017-01-01'
            ]
        );
        $setup->endSetup();
    }
}

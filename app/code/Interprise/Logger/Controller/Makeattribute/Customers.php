<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Index
 *
 * @author geuser1
 */

namespace Interprise\Logger\Controller\Makeattribute;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Config;
use Magento\Customer\Model\Customer;

class Customers extends \Magento\Framework\App\Action\Action
{
    public $_pageFactory;

    protected $setup;

    protected $eavSetupFactory;

    protected $eavConfig;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        EavSetupFactory $eavSetupFactory,
        Config $eavConfig,
        ModuleDataSetupInterface $setup
    ) {
        $this->setup = $setup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
        $this->_pageFactory = $pageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->setup]);
        $eavSetup->addAttribute(
            \Magento\Customer\Model\Indexer\Address\AttributeProvider::ENTITY,
            'interprise_apicreatedaddress',
            [
                'type' => 'varchar',
                'label' => 'Interprise Api created',
                'input' => 'text',
                'source' => '',
                'required' => false,
                'visible' => true,
                'position' => 800,
                'system' => false,
                'backend' => '',
            ]
        );
        $attribute = $this->eavConfig->getAttribute('customer_address', 'interprise_apicreatedaddress')
            ->addData([
                'used_in_forms' => [
                    'adminhtml_customer',
                    'adminhtml_checkout',
                    'customer_account_create',
                    'customer_account_edit',
                ],
            ]);
        $attribute->save();
    }
}

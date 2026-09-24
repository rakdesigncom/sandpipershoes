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
use Magento\Sales\Model\Order;

class Orders extends \Magento\Framework\App\Action\Action
{
    public $_pageFactory;

    protected $setup;

    protected $salesSetupFactory;

    protected $eavSetupFactory;

    protected $eavConfig;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Sales\Setup\SalesSetupFactory $salesSetupFactory,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        EavSetupFactory $eavSetupFactory,
        Config $eavConfig,
        ModuleDataSetupInterface $setup
    ) {
        $this->setup = $setup;
        $this->salesSetupFactory = $salesSetupFactory;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
        $this->_pageFactory = $pageFactory;

        parent::__construct($context);
    }

    public function execute()
    {
        $installer = $this->setup;

        $installer->startSetup();

        $salesSetup = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $installer]);

        $salesSetup->addAttribute(Order::ENTITY, 'interprise_apicreatedorder', [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length' => 255,
            'visible' => false,
            'nullable' => true,
        ]);
        $installer->endSetup();
    }
}

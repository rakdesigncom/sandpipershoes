<?php

namespace Fyb\Trade\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Model\Config;

class DropShipAddress implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        Config $eavConfig
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $eavSetup->addAttribute('customer_address', 'is_dropship', [
            'type' => 'int',
            'label' => 'Is Dropship',
            'input' => 'boolean',
            'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
            'group' => 'General',
            'required' => 0,
            'visible' => 0,
            'user_defined' => 1,
            'sort_order' => 1,
            'position' => 1,
            'system' => 0,
            'default' => 0,
        ]);

        $attribute = $this->eavConfig->getAttribute('customer_address', 'is_dropship');

        $attribute->setUsedInForms(['adminhtml_customer_address', 'adminhtml_customer'])
            ->save();

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }
}

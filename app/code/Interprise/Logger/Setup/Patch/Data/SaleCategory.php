<?php

namespace Interprise\Logger\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;

class SaleCategory implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    private $eavSetupFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
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
        $eavSetup->addAttribute(\Magento\Catalog\Model\Category::ENTITY, 'is_category_sale', [
            'type' => 'int',
            'label' => 'Is Category Sale',
            'input' => 'boolean',
            'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
            'visible' => true,
            'default' => '0',
            'required' => false,
            'sort_order' => 999,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            'group' => 'General Information',
        ]);

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public function revert()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        //Here should go code that will revert all operations from `apply` method
        //Please note, that some operations, like removing data from column, that is in role of foreign key reference
        //is dangerous, because it can trigger ON DELETE statement
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

<?php

namespace MagicToolbox\Magic360\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

/**
 * Upgrade Schema Script
 *
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if ($setup->tableExists('magic360_gallery')) {
            $tableName = $setup->getTable('magic360_gallery');
            $version = $context->getVersion();
            if ($version && version_compare($version, '1.0.3') < 0) {
                $setup->getConnection()->addIndex(
                    $tableName,
                    $setup->getIdxName(
                        'magic360_gallery',
                        ['product_id']
                    ),
                    ['product_id']
                );
            }
        }

        $setup->endSetup();
    }
}

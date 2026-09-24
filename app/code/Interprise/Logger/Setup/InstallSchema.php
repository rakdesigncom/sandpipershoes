<?php


namespace Interprise\Logger\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        $table_interprise_logger_cronmaster = $setup->getConnection()
            ->newTable($setup->getTable('interprise_logger_cronmaster'));
        $table_interprise_logger_cronmaster->addColumn(
            'cronmaster_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,],
            'Entity ID'
        );
        $table_interprise_logger_cronmaster->addColumn(
            'master_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => false,'nullable' => true,'primary' => false,'unsigned' => true,],
            'Master ID'
        );
         
        $table_interprise_logger_cronmaster->addColumn(
            'cron_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'cron_name'
        );
        $table_interprise_logger_cronmaster->addColumn(
            'cron_action',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'cron_action'
        );

        $table_interprise_logger_cronmaster->addColumn(
            'cron_function',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'cron_function'
        );

        $table_interprise_logger_cronmaster->addColumn(
            'cron_changelog_endpoint',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'cron_changelog_endpoint'
        );

        $table_interprise_logger_cronmaster->addColumn(
            'cron_frequency',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'in seconds'
        );

        $table_interprise_logger_cronmaster->addColumn(
            'cron_status',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            null,
            [],
            'cron_status'
        );

        $table_interprise_logger_cronmaster->addColumn(
            'cron_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            null,
            [],
            'cron_active'
        );

        $table_interprise_logger_cronmaster->addColumn(
            'cron_from_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            [],
            'from this point cron will run'
        );

        $table_interprise_logger_cronlog = $setup->getConnection()
            ->newTable($setup->getTable('interprise_logger_cronlog'));
        $table_interprise_logger_cronlog->addColumn(
            'cronlog_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,],
            'Entity ID'
        );

        $table_interprise_logger_cronlog->addColumn(
            'CronMasterId',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'CronMasterId'
        );

        $table_interprise_logger_cronlog->addColumn(
            'RunTime',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            [],
            'RunTime'
        );

        $table_interprise_logger_cronlog->addColumn(
            'Request',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Request'
        );

        $table_interprise_logger_cronlog->addColumn(
            'Response',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Response'
        );
        $table_interprise_logger_cronlog->addColumn(
            'Status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Status'
        );
        $table_interprise_logger_cronlog->addColumn(
            'Remarks',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Remarks'
        );

        $table_interprise_logger_cronlog->addColumn(
            'ActivityCount',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'ActivityCount'
        );
        $table_interprise_reattempt_frequency = $setup->getConnection()
            ->newTable($setup->getTable('interprise_reattempt_frequency'));
        $table_interprise_reattempt_frequency->addColumn(
            'reattempt_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,],
            'Entity ID'
        );
        $table_interprise_reattempt_frequency->addColumn(
            'Attempt_no',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Attempt_no'
        );
        $table_interprise_reattempt_frequency->addColumn(
            'Interval',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Interval'
        );
        $table_interprise_failed_orders = $setup->getConnection()
            ->newTable($setup->getTable('interprise_logger_failedorders'));
        $table_interprise_failed_orders->addColumn(
            'failedorder_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,],
            'Entity ID'
        );
        
        $table_interprise_failed_orders->addColumn(
            'Increment_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            45,
            ['nullable' => false, 'default' => '0'],
            'Increment_id'
        );

        $table_interprise_failed_orders->addColumn(
            'Changelog_item_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Changelog_item_id'
        );

        $table_interprise_failed_orders->addColumn(
            'Changelog_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Changelog_id'
        );

        $table_interprise_failed_orders->addColumn(
            'Reason',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Reason'
        );

        $table_interprise_failed_orders->addColumn(
            'Status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            64,
            ['nullable' => false, 'default' => '0'],
            'Status'
        );

        $table_interprise_failed_orders->addColumn(
            'Last_attempt',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            255,
            [],
            'Last_attempt'
        );
        $table_interprise_failed_orders->addColumn(
            'Attempt_no',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Attempt_no'
        );

        $table_interprise_failed_orders->addColumn(
            'Next_attempt',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            [],
            'Next_attempt'
        );   
        $table_interprise_failed_orders->addIndex(
            $setup->getIdxName(
                'interprise_logger_failedorders',
                ['Increment_id', 'Changelog_item_id', 'Changelog_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['Increment_id', 'Changelog_item_id', 'Changelog_id'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        );
        $table_interprise_logger_cronactivityschedule = $setup->getConnection()
            ->newTable($setup->getTable('interprise_logger_cronactivityschedule'));
        $table_interprise_logger_cronactivityschedule->addColumn(
            'cronactivityschedule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,],
            'Entity ID'
        );
        $table_interprise_logger_cronactivityschedule->addColumn(
            'CronLogId',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'CronLogId'
        );
        $table_interprise_logger_cronactivityschedule->addColumn(
            'CronMasterId',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            5,
            [],
            'CronMasterId'
        );
        $table_interprise_logger_cronactivityschedule->addColumn(
            'ActionType',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'ActionType'
        );
        $table_interprise_logger_cronactivityschedule->addColumn(
            'DataId',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'DataId'
        );
        $table_interprise_logger_cronactivityschedule->addColumn(
            'JsonData',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            1000,
            [],
            'JsonData'
        );
        $table_interprise_logger_cronactivityschedule->addColumn(
            'Status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Status'
        );
        $table_interprise_logger_cronactivityschedule->addColumn(
            'ActivityTime',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'ActivityTime'
        );
        $table_interprise_logger_cronactivityschedule->addColumn(
            'Remarks',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            1000,
            [],
            'Remarks'
        );
        $table_interprise_logger_cronactivityschedule->addColumn(
            'Request',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Request'
        );
        $table_interprise_logger_cronactivityschedule->addColumn(
            'Response',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Response'
        );
        $table_interprise_logger_cronactivityschedule->addColumn(
            'UpdateDate',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'UpdateDate'
        );
        $table_interprise_logger_cronactivityschedule->addColumn(
            'IsActive',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            null,
            [],
            'IsActive'
        );
        $table_interprise_logger_cronactivityschedule->addColumn(
            'ItemStatus',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'ItemStatus'
        );
        $table_interprise_logger_cronactivityschedule->addColumn(
            'site_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'site_id'
        );
        $table_interprise_logger_cronactivityschedule->addColumn(
            'priority',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'priority'
        );

        $table_interprise_logger_changelog = $setup->getConnection()
            ->newTable($setup->getTable('interprise_logger_changelog'));
        $table_interprise_logger_changelog->addColumn(
            'changelog_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,],
            'Entity ID'
        );
        $table_interprise_logger_changelog->addColumn(
            'CreatedAt',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'CreatedAt'
        );
        $table_interprise_logger_changelog->addColumn(
            'ItemType',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'ItemType'
        );
        $table_interprise_logger_changelog->addColumn(
            'ItemId',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'ItemId'
        );
        $table_interprise_logger_changelog->addColumn(
            'Action',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Action'
        );

        $table_interprise_logger_changelog->addColumn(
            'PushedStatus',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [],
            'PushedStatus'
        );
        $table_interprise_logger_changelog->addColumn(
            'JsonValue',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'JsonValue'
        );
        $setup->getConnection()->createTable($table_interprise_logger_changelog);

        $setup->getConnection()->createTable($table_interprise_logger_cronactivityschedule);

        $setup->getConnection()->createTable($table_interprise_logger_cronlog);

        $setup->getConnection()->createTable($table_interprise_logger_cronmaster);

        $setup->getConnection()->createTable($table_interprise_failed_orders);

        $setup->getConnection()->createTable($table_interprise_reattempt_frequency);
        
//        $setup->getConnection()->addColumn(
//        $setup->getTable('quote'),
//            'so_number',
//            [
//                'type' => 'varchar',
//                'nullable' => false,
//                'comment' => 'SO Number',
//            ]
//        );
//        $setup->getConnection()->addColumn(
//        $setup->getTable('quote'),
//            'is_invoice',
//            [
//                'type' => 'varchar',
//                'nullable' => false,
//                'comment' => 'IS Invoice number',
//            ]
//        );
//        $setup->getConnection()->addColumn(
//        $setup->getTable('sales_order'),
//            'so_number',
//            [
//                'type' => 'varchar',
//                'nullable' => false,
//                'comment' => 'SO Number',
//            ]
//        );
        
        //Create table of interprise_country_class_mapping
        $table_interprise_country_class_mapping = $setup->getConnection()
            ->newTable($setup->getTable('interprise_country_class_mapping'));
        $table_interprise_country_class_mapping->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,],
            'ID'
        );
        $table_interprise_country_class_mapping->addColumn(
            'iso_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'ISO Code'
        );
        $table_interprise_country_class_mapping->addColumn(
            'used_country',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Used Country'
        );
        $table_interprise_country_class_mapping->addColumn(
            'interprise_country',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Country in Interprise'
        );
        $table_interprise_country_class_mapping->addColumn(
            'interprise_customer_class',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Customer class in Interprise for this country'
        );
        $table_interprise_country_class_mapping->addColumn(
            'interprise_shipto_class',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Customer Shipto class in Interprise for this country'
        );
        $setup->getConnection()->createTable($table_interprise_country_class_mapping);
        //Create table of interprise_shipping_store_interprise
        $table_interprise_shipping_store_interprise = $setup->getConnection()
            ->newTable($setup->getTable('interprise_shipping_store_interprise'));
        $table_interprise_shipping_store_interprise->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,],
            'ID'
        );
        $table_interprise_shipping_store_interprise->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            0,
            [],
            'Store ID'
        );
        $table_interprise_shipping_store_interprise->addColumn(
            'store_shipping_method',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Shipping method in Store'
        );
        $table_interprise_shipping_store_interprise->addColumn(
            'interprise_shipping_method_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Shipping method used in Interprise'
        );
        $setup->getConnection()->createTable($table_interprise_shipping_store_interprise);
        //Create table of interprise_payment_methods
        $table_interprise_payment_methods = $setup->getConnection()
            ->newTable($setup->getTable('interprise_payment_methods'));
        $table_interprise_payment_methods->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,],
            'ID'
        );
        $table_interprise_payment_methods->addColumn(
            'is_payment_term_group',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            0,
            [],
            'Payment Term Group'
        );
        $table_interprise_payment_methods->addColumn(
            'is_payment_term_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Payment Term Code'
        );
        $table_interprise_payment_methods->addColumn(
            'is_payment_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Payment Type'
        );
        $table_interprise_payment_methods->addColumn(
            'is_payment_term_description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Description'
        );
        $table_interprise_payment_methods->addColumn(
            'default_payment_method',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'default_payment_method'
        );
        $table_interprise_payment_methods->addColumn(
            'is_isactive',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            1,
            [],
            'Status:: 0=>Inactive,1=>Active'
        );
        $table_interprise_payment_methods->addColumn(
            'magento_method',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Method used in Magento'
        );
//        $table_interprise_payment_methods->addColumn(
//            'default_payment_method',
//            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
//              255,
//            [],
//            'Default Payment Method'
//        );
//        $table_interprise_payment_methods->addColumn(
//            'default_payment_method_2',
//            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
//              255,
//            [],
//            'Default Payment Method'
//        );
//        $table_interprise_payment_methods->addColumn(
//            'payment_term_code_post',
//            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
//              255,
//            [],
//            'will be use in creating order of node payment term'
//        );
        $setup->getConnection()->createTable($table_interprise_payment_methods);
        //Create table of interprise_transaction_master
        $table_interprise_transaction_master = $setup->getConnection()
            ->newTable($setup->getTable('interprise_transaction_master'));
        $table_interprise_transaction_master->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,],
            'ID'
        );
        $table_interprise_transaction_master->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'customer_id'
        );
        $table_interprise_transaction_master->addColumn(
            'doc_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            20,
            [],
            'Document Type'
        );
        $table_interprise_transaction_master->addColumn(
            'document_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            20,
            [],
            'Document Code'
        );
        $table_interprise_transaction_master->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            [],
            'Updated time'
        );
        $table_interprise_transaction_master->addColumn(
            'sourcesalesordercode',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'sourcesalesordercode'
        );
        $table_interprise_transaction_master->addColumn(
            'rootdocumentcode',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'rootdocumentcode'
        );
        $table_interprise_transaction_master->addColumn(
            'pocode',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'pocode'
        );
        $table_interprise_transaction_master->addColumn(
            'salesorderdate',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'salesorderdate'
        );
        $table_interprise_transaction_master->addColumn(
            'duedate',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'duedate'
        );
        $table_interprise_transaction_master->addColumn(
            'shiptoname',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'shiptoname'
        );
        $table_interprise_transaction_master->addColumn(
            'paymenttermcode',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'paymenttermcode'
        );
        $table_interprise_transaction_master->addColumn(
            'total',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'total'
        );
        $table_interprise_transaction_master->addColumn(
            'balance',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'balance'
        );
        $table_interprise_transaction_master->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'status'
        );
        $table_interprise_transaction_master->addColumn(
            'isposted',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'isposted'
        );
        $table_interprise_transaction_master->addColumn(
            'isvoided',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'isvoided'
        );
        $setup->getConnection()->createTable($table_interprise_transaction_master);
        //Create table of interprise_transaction_detail
        $table_interprise_transaction_detail = $setup->getConnection()
            ->newTable($setup->getTable('interprise_transaction_detail'));
        $table_interprise_transaction_detail->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,],
            'ID'
        );
        $table_interprise_transaction_detail->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Magento Customer ID'
        );
    
        $table_interprise_transaction_detail->addColumn(
            'document_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            20,
            [],
            'Document Code'
        );
        $table_interprise_transaction_detail->addColumn(
            'json_data',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Data reponse'
        );
        $table_interprise_transaction_detail->addColumn(
            'json_detail',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'JSON Detail 1'
        );
        $table_interprise_transaction_detail->addColumn(
            'json_detail2',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'JSON Detail 2'
        );
        $setup->getConnection()->createTable($table_interprise_transaction_detail);
        //Create table of interprise_case
        $table_interprise_case = $setup->getConnection()->newTable($setup->getTable('interprise_case'));
        $table_interprise_case->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,],
            'Entity Id'
        );
        $table_interprise_case->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => false,'nullable' => true,'primary' => false,'unsigned' => true,],
            'store_id'
        );
        $table_interprise_case->addColumn(
            'case_number',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            100,
            [],
            'case_number'
        );
        $table_interprise_case->addColumn(
            'subject',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'subject'
        );
        $table_interprise_case->addColumn(
            'description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'description'
        );
        $table_interprise_case->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            [],
            'created_at'
        );
        $table_interprise_case->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            [],
            'updated_at'
        );
        $table_interprise_case->addColumn(
            'due_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            [],
            'due_at'
        );
        $table_interprise_case->addColumn(
            'end_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            [],
            'end_at'
        );
        $table_interprise_case->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'status'
        );
        $table_interprise_case->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'customer_id'
        );
        $table_interprise_case->addColumn(
            'from_created',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'from_created'
        );
        $table_interprise_case->addColumn(
            'priority',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'priority'
        );
        $table_interprise_case->addColumn(
            'magento_case_number',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'magento_case_number'
        );
        $setup->getConnection()->createTable($table_interprise_case);
        //Create table of interprise_custom_payment
        $table_interprise_custom_payment = $setup->getConnection()
            ->newTable($setup->getTable('interprise_custom_payment'));
        $table_interprise_custom_payment->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,],
            'Entity Id'
        );
        $table_interprise_custom_payment->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => false,'nullable' => true,'primary' => false,'unsigned' => true,],
            'customer_id'
        );
        $table_interprise_custom_payment->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            [],
            'created_at'
        );
        $table_interprise_custom_payment->addColumn(
            'amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            100,
            [],
            'amount'
        );
        $table_interprise_custom_payment->addColumn(
            'unique_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'unique_code'
        );
        $table_interprise_custom_payment->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            100,
            [],
            'status'
        );
        $table_interprise_custom_payment->addColumn(
            'payment_method',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'payment_method'
        );
        $table_interprise_custom_payment->addColumn(
            'is_receipt_no',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'is_receipt_no'
        );
        $table_interprise_custom_payment->addColumn(
            'receipt_status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'receipt_status'
        );
        $table_interprise_custom_payment->addColumn(
            'receipt_allocation_status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'receipt_allocation_status'
        );
       
        $setup->getConnection()->createTable($table_interprise_custom_payment);
        //Create table of interprise_custom_payment_item
        $table_interprise_custom_payment_item = $setup->getConnection()
            ->newTable($setup->getTable('interprise_custom_payment_item'));
        $table_interprise_custom_payment_item->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,],
            'id'
        );
        $table_interprise_custom_payment_item->addColumn(
            'payment_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => false,'nullable' => true,'primary' => false,'unsigned' => true,],
            'payment_id'
        );
         
        $table_interprise_custom_payment_item->addColumn(
            'itme_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            100,
            [],
            'itme_code'
        );
        $table_interprise_custom_payment_item->addColumn(
            'amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'amount'
        );
        $table_interprise_custom_payment_item->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            100,
            [],
            'created_at'
        );
        $setup->getConnection()->createTable($table_interprise_custom_payment_item);
        //Create table of interprise_price_lists
        $table_interprise_price_lists = $setup->getConnection()->newTable($setup->getTable('interprise_price_lists'));
        $table_interprise_price_lists->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,],
            'id'
        );
        $table_interprise_price_lists->addColumn(
            'itemcode',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            100,
            [],
            'itemcode'
        );
        $table_interprise_price_lists->addColumn(
            'pricelist',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'pricelist'
        );
        $table_interprise_price_lists->addColumn(
            'price',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'price'
        );
        $table_interprise_price_lists->addColumn(
            'from_qty',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'from_qty'
        );
        $table_interprise_price_lists->addColumn(
            'to_qty',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'to_qty'
        );
        $table_interprise_price_lists->addColumn(
            'currency',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'currency'
        );
        $table_interprise_price_lists->addColumn(
            'unitofmeasure',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'unitofmeasure'
        );
        $setup->getConnection()->createTable($table_interprise_price_lists);
        //Create table of interprise_pricing_customer
        $table_interprise_pricing_customer = $setup->getConnection()
            ->newTable($setup->getTable('interprise_pricing_customer'));
        $table_interprise_pricing_customer->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,],
            'id'
        );
        $table_interprise_pricing_customer->addColumn(
            'item_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            100,
            [],
            'item_code'
        );
        $table_interprise_pricing_customer->addColumn(
            'customer_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'customer_code'
        );
        $table_interprise_pricing_customer->addColumn(
            'price',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'price'
        );
        $table_interprise_pricing_customer->addColumn(
            'min_qty',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'min_qty'
        );
        $table_interprise_pricing_customer->addColumn(
            'qty_upto',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'qty_upto'
        );
        $table_interprise_pricing_customer->addColumn(
            'currency',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'currency'
        );
        $setup->getConnection()->createTable($table_interprise_pricing_customer);
        //Create table of interprise_statement_account
        $table_interprise_statement_account = $setup->getConnection()
            ->newTable($setup->getTable('interprise_statement_account'));
        $table_interprise_statement_account->addColumn(
            'statement_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,],
            'statement_id'
        );
        $table_interprise_statement_account->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            100,
            [],
            'customer_id'
        );
        $table_interprise_statement_account->addColumn(
            'invoice_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'invoice_code'
        );
        $table_interprise_statement_account->addColumn(
            'document_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            255,
            [],
            'document_date'
        );
        $table_interprise_statement_account->addColumn(
            'due_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            255,
            [],
            'due_date'
        );
        $table_interprise_statement_account->addColumn(
            'document_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'document_type'
        );
        $table_interprise_statement_account->addColumn(
            'balance_rate',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'balance_rate'
        );
        $table_interprise_statement_account->addColumn(
            'total_rate',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'total_rate'
        );
        $table_interprise_statement_account->addColumn(
            'reference',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'reference'
        );
        $setup->getConnection()->createTable($table_interprise_statement_account);
        
        $table_interprise_installwizard = $setup->getConnection()
            ->newTable($setup->getTable('interprise_install_wizard'));
        $table_interprise_installwizard->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,],
            'id'
        );
        $table_interprise_installwizard->addColumn(
            'item_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'item_name'
        );
        $table_interprise_installwizard->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'status'
        );
        $table_interprise_installwizard->addColumn(
            'action',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'action'
        );
        $table_interprise_installwizard->addColumn(
            'total_records',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            255,
            [],
            'total_records'
        );
        $table_interprise_installwizard->addColumn(
            'function_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'function_name'
        );
        $table_interprise_installwizard->addColumn(
            'sync_done',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            [],
            'sync_done'
        );
        $table_interprise_installwizard->addColumn(
            'sort_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            [],
            'sort order'
        );
        
        $setup->getConnection()->createTable($table_interprise_installwizard);
         
        $quote = 'quote';
        $orderTable = 'sales_order';

        $setup->getConnection()
            ->addColumn(
                $setup->getTable($quote),
                'so_number',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'comment' =>'SO Number'
                ]
            );
        //Order table
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($orderTable),
                'so_number',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'comment' =>'SO Number'
                ]
            );

        $setup->endSetup();
    }
}

<?php
/**
 * Magedelight
 * Copyright (C) 2019 Magedelight <info@magedelight.com>
 *
 * @category  Magedelight
 * @package   Magedelight_SMSProfile
 * @copyright Copyright (c) 2019 Mage Delight (http://www.magedelight.com/)
 * @license   http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author    Magedelight <info@magedelight.com>
 */
 
namespace Magedelight\SMSProfile\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements InstallSchemaInterface
{
    /* @codingStandardsIgnoreStart */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        /* @codingStandardsIgnoreEnd */
        $installer = $setup;
        $installer->startSetup();

        // Get magedelight_smsprofilelog table
        $tableName = $installer->getTable('magedelight_smsprofilelog');
        // Check if the table already exists
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            // Create magedelight_smsprofilelog table
            $table = $installer->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Entity Id'
                )
                ->addColumn(
                    's_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'message SId'
                )
                ->addColumn(
                    'api_service',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Api Service'
                )
                ->addColumn(
                    'recipient_phone',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    '64K',
                    ['nullable' => false],
                    'Recipient Phone Number'
                )
                ->addColumn(
                    'transaction_type',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => true, 'default' => null],
                    'Transaction Type'
                )
                ->addColumn(
                    'message_body',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    '64k',
                    ['nullable' => true, 'default' => null],
                    'Message Body'
                )
                ->addColumn(
                    'status',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => true, 'default' => null],
                    'Sms Status'
                )
                ->addColumn(
                    'is_error',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false],
                    'Result Is Error'
                )
                ->addColumn(
                    'error_message',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => true, 'default' => null],
                    'Sms Error Message'
                )
                ->addColumn(
                    'created_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                    null,
                    ['nullable' => true, 'default' => null],
                    'Created At'
                )
                ->setComment('SMSProfile Log Table');
            $installer->getConnection()->createTable($table);

            $installer->getConnection()->addIndex(
                $installer->getTable('magedelight_smsprofilelog'),
                $setup->getIdxName(
                    $installer->getTable('magedelight_smsprofilelog'),
                    ['s_id ','api_service'],
                    AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                [
                    's_id',
                    'api_service',
                    'recipient_phone',
                    'transaction_type',
                    'message_body',
                    'status',
                    'error_message'
                ],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            );

        }

        // Get magedelight_smsprofiletemplates table
        $tableName = $installer->getTable('magedelight_smsprofiletemplates');
        // Check if the table already exists
        if ($installer->getConnection()->isTableExists($tableName) != true) {

             // Create magedelight_smsprofiletemplates table
            $table = $installer->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Entity Id'
                )
                ->addColumn(
                    'template_name',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Template Name'
                )
                ->addColumn(
                    'template_content',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    '64k',
                    ['nullable' => true, 'default' => null],
                    'Template Content'
                )
                ->addColumn(
                    'event_type',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => true, 'default' => null],
                    'Sms Event Type'
                )
                ->addColumn(
                    'store_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => true, 'default' => null],
                    'Store Id'
                )
                ->setComment('SMSProfile Templates Table');
                $installer->getConnection()->createTable($table);

                $installer->getConnection()->addIndex(
                    $installer->getTable('magedelight_smsprofiletemplates'),
                    $setup->getIdxName(
                        $installer->getTable('magedelight_smstemplates'),
                        ['template_name ','template_content'],
                        AdapterInterface::INDEX_TYPE_FULLTEXT
                    ),
                    [
                        'template_name',
                        'template_content',
                        'event_type',
                    ],
                    AdapterInterface::INDEX_TYPE_FULLTEXT
                );

        }

         // Get magedelight_smsprofileotp table
        $tableName = $installer->getTable('magedelight_smsprofileotp');
        // Check if the table already exists
        if ($installer->getConnection()->isTableExists($tableName) != true) {

             // Create magedelight_smsprofileotp table
            $table = $installer->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Entity Id'
                )->addColumn(
                    'otp_code',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'OTP Code'
                )->addColumn(
                    'customer_mobile',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Customer Mobile'
                )->addColumn(
                    'resend',
                    Table::TYPE_INTEGER,
                    11,
                    ['nullable' => false, 'default' => '0'],
                    'resend'
                )->addColumn(
                    'created_at',
                    Table::TYPE_DATETIME,
                    null,
                    ['nullable' => true, 'default' => null],
                    'Creation Time'
                )
                ->addColumn(
                    'updated_at',
                    Table::TYPE_DATETIME,
                    null,
                    ['nullable' => true, 'default' => null],
                    'Update Time'
                )->addColumn(
                    'timeout',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Time Out For Otp'
                )->setComment(
                    'SMSProfile OTP Table'
                );
                $installer->getConnection()->createTable($table);

                $installer->getConnection()->addIndex(
                    $installer->getTable('magedelight_smsprofileotp'),
                    $setup->getIdxName(
                        $installer->getTable('magedelight_smsprofileotp'),
                        ['otp_code'.'customer_mobile'],
                        AdapterInterface::INDEX_TYPE_FULLTEXT
                    ),
                    [
                        'otp_code',
                        'customer_mobile',
                    ],
                    AdapterInterface::INDEX_TYPE_FULLTEXT
                );

        }
        
        $installer->endSetup();
    }
}

<?php

namespace Siscom\RecuperaCarrinho\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Siscom\RecuperaCarrinho\Api\Data\RecoveredCartInterface;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        // Get table name
        $tableName = $setup->getTable(RecoveredCartInterface::TABLE_NAME);

        // Check if table already exists
        if (!$setup->getConnection()->isTableExists($tableName)) {
            // Create table
            $table = $setup->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    RecoveredCartInterface::ID,
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'nullable' => false, 'primary' => true],
                    'ID'
                )
                ->addColumn(
                    RecoveredCartInterface::CUSTOMER_ID,
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => true],
                    'Customer ID'
                )
                ->addColumn(
                    RecoveredCartInterface::CUSTOMER_EMAIL,
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Customer Email'
                )
                ->addColumn(
                    RecoveredCartInterface::CART_ID,
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => true],
                    'Cart ID'
                )
                ->addColumn(
                    RecoveredCartInterface::EMAIL_SENT,
                    Table::TYPE_BOOLEAN,
                    1,
                    ['nullable' => false, 'default' => 0],
                    'Is Sent'
                )
                ->addColumn(
                    RecoveredCartInterface::CREATED_AT,
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                    'Created At'
                )
                ->setComment('Recovered Shopping Carts');

            $setup->getConnection()->createTable($table);
        }

        $setup->endSetup();
    }
}

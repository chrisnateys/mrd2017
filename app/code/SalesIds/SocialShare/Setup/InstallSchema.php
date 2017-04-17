<?php
/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIds\SocialShare\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $connection = $setup->getConnection();

        $installer->startSetup();

        /**
         * Tables
         */
        $serviceTable = $installer->getTable('salesids_socialshare_service');

        /**
         * Create table 'salesids_socialshare_service' if not exists
         */
        if (!$installer->getConnection()->isTableExists($serviceTable)) {
            $table = $installer->getConnection()->newTable(
                $serviceTable
            )->addColumn(
                'service_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Service Id'
            )->addColumn(
                'code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['nullable' => true],
                'Code'
            )->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['nullable' => true],
                'Name'
            )->addColumn(
                'url',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'URL'
            )->addColumn(
                'color',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                8,
                ['unsigned' => true, 'default' => '0'],
                'Color'
            )->addColumn(
                'icon',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => true],
                'Icon'
            )->addColumn(
                'can_numbered',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['nullable' => true, 'default' => 0],
                'Can Be Numbered'
            )->addIndex(
                $installer->getIdxName('salesids_socialshare_service', ['url']),
                ['url']
            )->setComment(
                'SalesIds Social Share Service Table'
            );
            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}

<?php
/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIds\SocialShare\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Upgrades data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $connection = $setup->getConnection();
        $serviceTable = $setup->getTable('salesids_socialshare_service');

        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $column = [
                'type' => Table::TYPE_BOOLEAN,
                'nullable' => false,
                'comment' => 'Is Selectable',
                'default' => 1
            ];
            $connection->addColumn($serviceTable, 'selectable', $column);
        }

        $setup->endSetup();
    }
}

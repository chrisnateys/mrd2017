<?php
/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIds\SocialShare\Setup;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * Upgrades data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $setup->getConnection()->insert($setup->getTable('salesids_socialshare_service'), [
                'code' => 'more',
                'name' => 'More Button',
                'url' => '',
                'color' => '#94d228',
                'icon' => 'fa fa-plus',
                'can_numbered' => 0,
                'selectable' => 0
            ]);
            $setup->getConnection()->insert($setup->getTable('salesids_socialshare_service'), [
                'code' => 'favorite',
                'name' => 'Favorite',
                'url' => '',
                'color' => '#ffb227',
                'icon' => 'fa fa-star-o',
                'can_numbered' => 0
            ]);
            $setup->getConnection()->insert($setup->getTable('salesids_socialshare_service'), [
                'code' => 'windowprint',
                'name' => 'Print',
                'url' => '',
                'color' => '#000000',
                'icon' => 'fa fa-print',
                'can_numbered' => 0
            ]);
        }

        $setup->endSetup();
    }
}

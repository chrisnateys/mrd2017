<?php
/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIds\SocialShare\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * Installs data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $services = [
            'twitter' => [
                'code' => 'twitter',
                'name' => 'Twitter',
                'url' => 'http://twitter.com/intent/tweet?status={page.title}+{page.url}',
                'color' => '#4da7de',
                'icon' => 'socicon-twitter',
                'can_numbered' => 0
            ],
            'pinterest' => [
                'code' => 'pinterest',
                'name' => 'Pinterest',
                'url' => 'http://pinterest.com/pin/create/bookmarklet/?url={page.url}&description={page.title}&media={product.image}',
                'color' => '#c92619',
                'icon' => 'socicon-pinterest',
                'can_numbered' => 1
            ],
            'facebook' => [
                'code' => 'facebook',
                'name' => 'Facebook',
                'url' => 'https://www.facebook.com/sharer.php?p[url]={page.url}&p[title]={page.title}&p[summary]={page.description}',
                'color' => '#3e5b98',
                'icon' => 'socicon-facebook',
                'can_numbered' => 1
            ],
            'googleplus' => [
                'code' => 'googleplus',
                'name' => 'Google+',
                'url' => 'https://plus.google.com/share?url={page.url}',
                'color' => '#dd4b39',
                'icon' => 'socicon-googleplus',
                'can_numbered' => 1
            ],
            'reddit' => [
                'code' => 'reddit',
                'name' => 'Reddit',
                'url' => 'http://www.reddit.com/submit?url={page.url}&title={page.title}',
                'color' => '#e74a1e',
                'icon' => 'socicon-reddit',
                'can_numbered' => 0
            ],
            'delicious' => [
                'code' => 'delicious',
                'name' => 'Delicious',
                'url' => 'http://del.icio.us/post?url={page.url}&title={page.title}',
                'color' => '#020202',
                'icon' => 'socicon-delicious',
                'can_numbered' => 0
            ],
            'digg' => [
                'code' => 'digg',
                'name' => 'Digg',
                'url' => 'https://digg.com/submit?url={page.url}&title={page.title}',
                'color' => '#1d1d1b',
                'icon' => 'socicon-digg',
                'can_numbered' => 0
            ],
            'stumbleupon' => [
                'code' => 'stumbleupon',
                'name' => 'StumbleUpon',
                'url' => 'http://www.stumbleupon.com/submit?url={page.url}&title={page.title}',
                'color' => '#e64011',
                'icon' => 'socicon-stumbleupon',
                'can_numbered' => 0
            ],
            'linkedin' => [
                'code' => 'linkedin',
                'name' => 'LinkedIn',
                'url' => 'http://www.linkedin.com/shareArticle?mini=true&url={page.url}&title={page.title}',
                'color' => '#3371b7',
                'icon' => 'socicon-linkedin',
                'can_numbered' => 0
            ],
            'tumblr' => [
                'code' => 'tumblr',
                'name' => 'Tumblr',
                'url' => 'http://www.tumblr.com/share?v=3&u={page.url}&t={page.title}',
                'color' => '#45556c',
                'icon' => 'socicon-tumblr',
                'can_numbered' => 0
            ],
            'newsvine' => [
                'code' => 'newsvine',
                'name' => 'Newsvine',
                'url' => 'http://www.newsvine.com/_tools/seed&save?u={page.url}&h={page.title}',
                'color' => '#075B2F',
                'icon' => 'socicon-newsvine',
                'can_numbered' => 0
            ],
            'whatsapp' => [
                'code' => 'whatsapp',
                'name' => 'WhatsApp',
                'url' => 'whatsapp://send?text={page.url}',
                'color' => '#20B038',
                'icon' => 'socicon-whatsapp',
                'can_numbered' => 0
            ],
            'mail' => [
                'code' => 'mail',
                'name' => 'E-mail',
                'url' => '',
                'color' => '#000000',
                'icon' => 'socicon-mail',
                'can_numbered' => 0
            ]
        ];

        foreach ($services as $service) {
            // insert a simple row into "salesids_installer_test" table
            $setup->getConnection()->insertForce(
                $setup->getTable('salesids_socialshare_service'),
                $service
            );
        }

        $setup->endSetup();
    }
}

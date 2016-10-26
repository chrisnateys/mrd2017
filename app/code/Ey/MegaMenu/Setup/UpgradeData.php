<?php

namespace Ey\MegaMenu\Setup;

use \Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * Category setup factory
     *
     * @var \Magento\Catalog\Setup\CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    public function __construct(
        EavSetupFactory $eavSetupFactory,
        \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->categorySetupFactory = $categorySetupFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '0.0.3') < 0) {
            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);

            $categorySetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'megamenu_image',
                [
                    'group' => 'Mega Menu',
                    'type' => 'varchar',
                    'label' => 'Featured Image',
                    'name' => 'Featured Image',
                    'input' => 'image',
                    'backend' => 'Magento\Catalog\Model\Category\Attribute\Backend\Image',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'visible' => true,
                    'visible_on_front' => true,
                    'required' => false,
                    'user_defined' => true
                ]
            );

            $categorySetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'megamenu_image_url',
                [
                    'label'      => 'Featured Image Url',
                    'name'      => 'Featured Image Url',
                    'group'     => 'Mega Menu',
                    'required'  => false,
                    'type' => 'text',
                    'input' => 'text',
                    'comment' => 'Absolute URL',
                    'backend' => '',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'visible_on_front' => true
                ]
            );
        }

        if (version_compare($context->getVersion(), '0.0.6') < 0) {
            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);

            $categorySetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'megamenu_html',
                [
                    'label'      => 'Mega Menu',
                    'name'      => 'Mega Menu',
                    'group'     => 'Mega Menu',
                    'required'  => false,
                    'type' => 'text',
                    'input' => 'editor',
                    'backend' => '',
                    'is_wysiwyg_enabled' => 1,
                    'is_html_allowed_on_front' => 1,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'visible_on_front' => true
                ]
            );
        }

        if (version_compare($context->getVersion(), '0.0.8') < 0) {
            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);

            $categorySetup->removeAttribute(\Magento\Catalog\Model\Category::ENTITY, 'megamenu_static_block');

            $categorySetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'megamenu_static_block',
                [
                    'label'      => 'Static Block',
                    'name'      => 'Static Block',
                    'group'     => 'Mega Menu',
                    'required'  => false,
                    'type' => 'int',
                    'input' => 'select',
                    'backend' => '',
                    'source' => 'Ey\MegaMenu\Model\Config\Source\Block',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'visible_on_front' => true
                ]
            );
        }
    }
}
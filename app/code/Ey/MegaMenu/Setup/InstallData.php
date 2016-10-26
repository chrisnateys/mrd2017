<?php

namespace Ey\MegaMenu\Setup;

use Magento\Framework\Setup;

class InstallData implements Setup\InstallDataInterface
{
    /**
     * Category setup factory
     *
     * @var \Magento\Catalog\Setup\CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     * Init
     *
     * @param \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory
     */
    public function __construct(\Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory)
    {
        $this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * @param Setup\ModuleDataSetupInterface $setup
     * @param Setup\ModuleContextInterface $moduleContext
     */
    public function install(Setup\ModuleDataSetupInterface $setup, Setup\ModuleContextInterface $moduleContext)
    {
        $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);

        $categorySetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'megamenu_activate',
            [
                'label'      => 'Activate',
                'name'      => 'Activate',
                'group'     => 'Mega Menu',
                'required'  => false,
                'type' => 'int',
                'input' => 'select',
                'backend' => '',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'visible_on_front' => true
            ]
        );
    }

}

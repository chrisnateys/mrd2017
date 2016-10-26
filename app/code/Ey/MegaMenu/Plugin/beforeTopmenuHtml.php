<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ey\MegaMenu\Plugin;


class beforeTopmenuHtml extends \Magento\Catalog\Observer\MenuCategoryData
{
    /**
     * @param \Magento\Framework\Data\Tree\Node $category
     * @return array
     */
    public function aroundGetMenuCategoryData
    (
        \Magento\Catalog\Observer\MenuCategoryData $subject,
        \Closure $proceed,
        $category
    )
    {
        $categoryData = $proceed($category);
        $categoryData['megamenu_activate'] = $category->getMegamenuActivate() == '1' ? true:false;
        $categoryData['megamenu_image'] = $category->getMegamenuImage();
        $categoryData['megamenu_image_url'] = $category->getMegamenuImageUrl();
        $categoryData['megamenu_html'] = $category->getMegamenuHtml();
        $categoryData['megamenu_static_block'] = $category->getMegamenuStaticBlock();

        return $categoryData;
    }
}

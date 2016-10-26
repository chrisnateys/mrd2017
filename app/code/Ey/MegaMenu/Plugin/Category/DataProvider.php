<?php

namespace Ey\MegaMenu\Plugin\Category;

use Magento\Store\Model\StoreManagerInterface;

/**
 * Class DataProvider
 * @package Ey\MegaMenu\Plugin\Category
 */
class DataProvider
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * DataProvider constructor.
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function afterGetData(\Magento\Catalog\Model\Category\DataProvider $subject, $categoryData)
    {
        /** @var \Magento\Catalog\Model\Category $category */
        $category = $subject->getCurrentCategory();
        if ($category && $categoryData) {
            $baseMedia = $this->storeManager->getStore()->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ) . 'catalog/category/';
            if (isset($categoryData[$category->getId()]['desktop_banner'])) {
                unset($categoryData[$category->getId()]['desktop_banner']);
                $categoryData[$category->getId()]['desktop_banner'][0]['name'] = $category->getData('desktop_banner');
                $categoryData[$category->getId()]['desktop_banner'][0]['url'] = $baseMedia . $category->getData('desktop_banner');
            } if (isset($categoryData[$category->getId()]['tablet_banner'])) {
                unset($categoryData[$category->getId()]['tablet_banner']);
                $categoryData[$category->getId()]['tablet_banner'][0]['name'] = $category->getData('tablet_banner');
                $categoryData[$category->getId()]['tablet_banner'][0]['url'] = $baseMedia . $category->getData('tablet_banner');
            } if (isset($categoryData[$category->getId()]['mobile_banner'])) {
                unset($categoryData[$category->getId()]['mobile_banner']);
                $categoryData[$category->getId()]['mobile_banner'][0]['name'] = $category->getData('mobile_banner');
                $categoryData[$category->getId()]['mobile_banner'][0]['url'] = $baseMedia . $category->getData('mobile_banner');
            } if (isset($categoryData[$category->getId()]['megamenu_image'])) {
                unset($categoryData[$category->getId()]['megamenu_image']);
                $categoryData[$category->getId()]['megamenu_image'][0]['name'] = $category->getData('megamenu_image');
                $categoryData[$category->getId()]['megamenu_image'][0]['url'] = $baseMedia . $category->getData('megamenu_image');
            }
        }
        return $categoryData;
    }
}
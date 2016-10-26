<?php

namespace Ey\MegaMenu\Controller\Adminhtml\Category;

/**
 * Class Edit
 * @package Ey\MegaMenu\Controller\Adminhtml\Category
 */
class Edit extends \Magento\Catalog\Controller\Adminhtml\Category\Edit
{
    /**
     * Edit category page
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $storeId = (int)$this->getRequest()->getParam('store');
        $store = $this->getStoreManager()->getStore($storeId);
        $this->getStoreManager()->setCurrentStore($store->getCode());

        $categoryId = (int)$this->getRequest()->getParam('id');

        if (!$categoryId) {
            if ($storeId) {
                $categoryId = (int)$this->getStoreManager()->getStore($storeId)->getRootCategoryId();
            } else {
                $defaultStoreView = $this->getStoreManager()->getDefaultStoreView();
                if ($defaultStoreView) {
                    $categoryId = (int)$defaultStoreView->getRootCategoryId();
                } else {
                    $stores = $this->getStoreManager()->getStores();
                    if (count($stores)) {
                        $store = reset($stores);
                        $categoryId = (int)$store->getRootCategoryId();
                    }
                }
            }
            $this->getRequest()->setParam('id', $categoryId);
        }

        $category = $this->_initCategory(true);
        if (!$category || $categoryId != $category->getId() || !$category->getId()) {
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('catalog/*/', ['_current' => true, 'id' => null]);
        }

        /**
         * Check if there are data in session (if there was an exception on saving category)
         */
        $categoryData = $this->_getSession()->getCategoryData(true);
        if (is_array($categoryData)) {
            if (isset($categoryData['image']['delete'])) {
                $categoryData['image'] = null;
            } else {
                unset($categoryData['image']);
            }
            if (isset($categoryData['desktop_banner']['delete'])) {
                $categoryData['desktop_banner'] = null;
            } else {
                unset($categoryData['desktop_banner']);
            }
            if (isset($categoryData['tablet_banner']['delete'])) {
                $categoryData['tablet_banner'] = null;
            } else {
                unset($categoryData['tablet_banner']);
            }
            if (isset($categoryData['mobile_banner']['delete'])) {
                $categoryData['mobile_banner'] = null;
            } else {
                unset($categoryData['mobile_banner']);
            }
            if (isset($categoryData['megamenu_image']['delete'])) {
                $categoryData['megamenu_image'] = null;
            } else {
                unset($categoryData['megamenu_image']);
            }
            $category->addData($categoryData);
        }

        if ($category->getData('megamenu_html')) {
            $category->setData('megamenu', $category->getData('megamenu_html'));
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        if ($this->getRequest()->getQuery('isAjax')) {
            return $this->ajaxRequestResponse($category, $resultPage);
        }

        $resultPage->setActiveMenu('Magento_Catalog::catalog_categories');
        $resultPage->getConfig()->getTitle()->prepend(__('Categories'));
        $resultPage->getConfig()->getTitle()->prepend($categoryId ? $category->getName() : __('Categories'));
        $resultPage->addBreadcrumb(__('Manage Catalog Categories'), __('Manage Categories'));

        $block = $resultPage->getLayout()->getBlock('catalog.wysiwyg.js');
        if ($block) {
            $block->setStoreId($storeId);
        }

        return $resultPage;
    }

    /**
     * @return \Magento\Store\Model\StoreManagerInterface
     */
    private function getStoreManager()
    {
        if (null === $this->storeManager) {
            $this->storeManager = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magento\Store\Model\StoreManagerInterface');
        }
        return $this->storeManager;
    }
}
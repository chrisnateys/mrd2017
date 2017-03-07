<?php
/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIds\SocialShare\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Registry;
use Magento\Framework\View\Page\Config;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{
    /**
     * Config paths
     */
    const XML_PATH_EMAIL_SUBJECT = 'general/share/salesids_socialshare_email_subject';
    const XML_PATH_EMAIL_BODY = 'general/share/salesids_socialshare_email_body';

    /**
     * ACL resources
     */
    const ACL_SERVICE  = 'SalesIds_SocialShare::service';

    /**
     * Replacement tags
     *
     * @var array
     */
    protected $_replacementTags;

    /**
     * Menu
     */
    const MENU_SERVICE = 'SalesIds_SocialShare::service';

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * Page configuration
     *
     * @var Config
     */
    protected $_pageConfig;

    /**
     * Retrieve currently viewed product object
     *
     * @return \Magento\Catalog\Model\Product
     */
    protected function _getProduct()
    {
        return $this->_coreRegistry->registry('product');
    }

    /**
     * Get replacement tags
     *
     * @return array
     */
    protected function _getReplacementTags()
    {
        if (!$this->_replacementTags) {
            $this->_replacementTags = [
                'page.url' => $this->_urlBuilder->getCurrentUrl(),
                'page.title' => $this->_pageConfig->getTitle()->get(),
                'page.description' => $this->_pageConfig->getDescription(),
                'product.image' => $this->_getProductImageURL(),
                'product.name' => $this->_getProductData('name')
            ];
        }
        return $this->_replacementTags;
    }

    /**
     * Get product image URL
     *
     * @return string
     */
    protected function _getProductImageURL()
    {
        $product = $this->_getProduct();
        if (!$product) {
            return '';
        }
        $images = $product->getMediaGalleryImages();
        if (!$images || empty($images)) {
            return '';
        }

        // Get first image URL
        foreach ($images as $image) {
            return $image->getUrl();
        }
        return '';
    }

    /**
     * Get product data using key
     *
     * @param string $key
     * @return string
     */
    protected function _getProductData($key)
    {
        $product = $this->_getProduct();
        if (!$product || !$product->getId()) {
            return '';
        }
        return $product->getData($key);
    }

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param Config $pageConfig
     */
    public function __construct(
        Context $context,
        Config $pageConfig,
        Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_pageConfig = $pageConfig;
        return parent::__construct($context);
    }

    /**
     * Get email subject
     *
     * @return string
     */
    public function getEmailSubject()
    {
        return __($this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_SUBJECT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ));
    }

    /**
     * Get email body
     *
     * @return string
     */
    public function getEmailBody()
    {
        return __($this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_BODY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ));
    }

    /**
     * Get tag value
     *
     * @param string $value
     * @param SalesIds\SocialShare\Block\Widget\ServicesWidget $widget
     * @return string
     */
    public function replaceTags($value, $widget)
    {
        preg_match_all('/\{(.*?)\}/s', $value, $tags);

        // If no tags found
        if (!$tags || !isset($tags[0]) || empty($tags[0])) {
            return $value;
        }

        // Replace tags into string
        foreach ($tags[0] as $tag) {
            $value = str_replace($tag, $this->replaceTag($tag, $widget), $value);
        }
        return $value;
    }

    /**
     * Get tag value
     *
     * @param string $value
     * @param SalesIds\SocialShare\Block\Widget\ServicesWidget $widget
     * @return string
     */
    public function replaceTag($value, $widget)
    {
        $value = preg_replace('/\{|\}/s', '', $value);
        $param = $value;
        $replacementTags = $this->_getReplacementTags();

        // Support multiple params separated by |
        $paramValues = explode('|', $value);
        $param = str_replace('|', '', $param);

        foreach ($paramValues as $paramValue) {

            // Allow change before
            $this->_eventManager->dispatch('salesids_servicewidget_param_before', [
                'widget' => $widget->getWidget(),
                'service' => $widget->getService(),
                'tag' => &$paramValue,
                'param' => &$param,
                'replacement_tags' => &$replacementTags
            ]);

            if (isset($replacementTags[$paramValue])) {
                $param = $replacementTags[$paramValue];

                // Allow last change of parameter
                $this->_eventManager->dispatch('salesids_servicewidget_param_after', [
                    'widget' => $widget->getWidget(),
                    'service' => $widget->getService(),
                    'tag' => &$paramValue,
                    'param' => &$param,
                    'replacement_tags' => &$replacementTags
                ]);

                break;
            }

            // Remove non used element
            $param = str_replace($paramValue, '', $param);
        }

        return $param;
    }
}

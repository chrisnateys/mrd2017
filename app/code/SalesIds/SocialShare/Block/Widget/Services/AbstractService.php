<?php
/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIds\SocialShare\Block\Widget\Services;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Page\Config;
use Magento\Widget\Block\BlockInterface;
use SalesIds\SocialShare\Helper\Data as DataHelper;

abstract class AbstractService extends Template implements BlockInterface
{
    /**
     * Default template
     * @var string
     */
    const DEFAULT_TEMPLATE = 'widget/social_share/services/standard/standard.phtml';

    /**
     * Counter position top
     * @var string
     */
    const COUNTER_POSITION_TOP = 'box_count';

    /**
     * Type top countered button
     * @var string
     */
    protected $_typeNumberedTop = '';

    /**
     * Type above countered button
     * @var string
     */
    protected $_typeNumberedBeside = '';

    /**
     * Data helper
     * @var DataHelper
     */
    protected $_dataHelper;

    /**
     * Get is fixed container on page
     *
     * @return bool
     */
    protected function _getIsFixed()
    {
        $widget = $this->getWidget();
        return $widget->getDisplayType() == $widget::DISPLAY_TYPE_FIXED;
    }

    /**
     * Is element horizontal
     *
     * @return bool
     */
    protected function _getIsHorizontal()
    {
        $widget = $this->getWidget();
        if ($widget->getIsFixed()) {
            return in_array($widget->getScreenPosition(), [
                $widget::POSITION_TOP,
                $widget::POSITION_BOTTOM
            ]);
        }
        return true;
    }

    /**
     * Get is button type numbered
     *
     * @return bool
     */
    protected function _getIsNumbered()
    {
        $widget = $this->getWidget();
        return $widget->getButtonType() === $widget::BUTTON_TYPE_NUMBERED;
    }

    /**
     * Get minimal size of button height or width
     *
     * @return int
     */
    protected function _getMinimalSize()
    {
        $widget = $this->getWidget();
        $size = $widget->getIconWidth();
        if ($size > $widget->getIconHeight()) {
            $size = $widget->getIconHeight();
        }
        return $size;
    }

    /**
     * Get font size
     *
     * @return float
     */
    protected function _getIconFontSize()
    {
        $widget = $this->getWidget();
        switch ($widget->getButtonStyle()) {
            case $widget::BUTTON_STYLE_ROUND:
                $size = floor($this->_getMinimalSize() * 70 / 100);
                break;
            case $widget::BUTTON_STYLE_ROUNDED:
                $size = floor($this->_getMinimalSize() * 80 / 100);
                break;
            default:
                $size = floor($this->_getMinimalSize() * 88 / 100);
                break;
        }
        return $size;
    }

    /**
     * Constructor
     *
     * @param Context $context
     * @param DataHelper $dataHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        DataHelper $dataHelper,
        array $data = []
    ) {
        $this->_dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }

    /**
     * Can display
     *
     * @return boolean
     */
    public function canDisplay()
    {
        return true;
    }

    /**
     * Is display couter on top
     *
     * @return boolean
     */
    public function isDisplayCounterTop()
    {
        $widget = $this->getWidget();
        return $widget->getCounterPosition() == self::COUNTER_POSITION_TOP;
    }

    /**
     * Get style
     *
     * @return string
     */
    public function getStyle()
    {
        $widget = $this->getWidget();
        $style = sprintf(
            'font-size:%s; height:%s; width:%s;',
            $this->_getIconFontSize() . 'px',
            '82%',
            $widget->getIconWidth() . 'px'
        );

        if ($widget->getIconColor()) {
            $style .= sprintf('color:%s;', $widget->getIconColor());
        }

        switch ($widget->getScreenPosition()) {
            case $widget::POSITION_LEFT:
                $style .= ' float=left;';
                break;
            case $widget::POSITION_RIGHT:
                $style .= ' float=right;';
                break;
            default:
                // Nothing to do
                break;

        }
        return $style;
    }

    /**
     * Get button class name
     *
     * @return string
     */
    public function getClass()
    {
        return $this->getIcon();
    }

    /**
     * Get url of service
     *
     * @return string
     */
    public function getLinkUrl()
    {
        $url = $this->getServiceUrl();
        if (!$url) {
            return '';
        }
        $parts = parse_url($url);
        if (!isset($parts['query'])) {
            return $url;
        }
        parse_str($parts['query'], $params);
        $params = $this->_treatParams($params);
        return str_replace($parts['query'], http_build_query($params), $url);
    }

    /**
     * Parameters treatment
     *
     * @param string|array $params
     * @return array
     */
    protected function _treatParams($params)
    {
        if (!is_array($params)) {
            $params = explode(' ', $params);
            $elements = [];
            foreach ($params as $param) {
                $elements[] = $this->_dataHelper->replaceTag($param, $this->getWidget());
            }
            return implode(' ', $elements);
        }
        foreach ($params as $key => $value) {
            $newValue = $this->_treatParams($value);
            if (!$newValue) {
                unset($params[$key]);
                continue;
            }
            $params[$key] = $newValue;
        }
        return $params;
    }

    /**
     * Get service type
     *
     * @return string
     */
    public function getType()
    {
        $widget = $this->getWidget();
        if ($widget->getIsNumbered()) {
            return self::SERVICE_TYPE_NUMBERED;
        }
        return self::SERVICE_TYPE_STANDARD;
    }

    /**
     * Get type to display
     *
     * @return string
     */
    public function getNumberedType()
    {
        if ($this->isDisplayCounterTop()) {
            return $this->_typeNumberedTop;
        }
        return $this->_typeNumberedBeside;
    }

    /**
     * Get button size
     *
     * @return string
     */
    public function getButtonSize()
    {
        $widget = $this->getWidget();
        if ($widget->getData('button_size')) {
            return $widget->getData('button_size');
        }
        return $widget::DEFAULT_BUTTON_SIZE;
    }

    /**
     * Get container size
     *
     * @param SalesIds\SocialShare\Model\Service $service
     * @return string
     */
    public function getContainerStyle()
    {
        $widget = $this->getWidget();
        if ($this->getIsHorizontal() && $this->getIsFixed()) {
            $containerSize = $widget->getIconWidth() * sizeof($this->getServices());
            return sprintf('width:%spx', $containerSize);
        }
        return '';
    }
}

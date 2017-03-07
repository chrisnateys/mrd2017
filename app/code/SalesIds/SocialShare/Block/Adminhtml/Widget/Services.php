<?php
/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIds\SocialShare\Block\Adminhtml\Widget;

use Magento\Backend\Block\Template;
use Magento\Framework\DB\MapperInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\CatalogWidget\Model\Rule;
use Magento\Framework\Registry;
use SalesIds\SocialShare\Model\ResourceModel\Service\CollectionFactory;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

class Services extends Template implements RendererInterface
{
    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Widget\Model\Widget\Instance
     */
    protected $_widget;

    /**
     * Current service block
     *
     * @var \SalesIds\SocialShare\Block\Adminhtml\Widget\Services\Renderer
     */
    protected $_block;

    /**
     * @var string
     */
    protected $_elementName = '';

    /**
     * @var array|SalesIds\SocialShare\Model\ResourceModel\Service\Collection
     */
    protected $_selectedServices = [];

    /**
     * @var string
     */
    protected $_template = 'SalesIds_SocialShare::widget/services.phtml';

    /**
     * Get is numbered element
     *
     * @return bool
     */
    protected function _getIsNumberedElement()
    {
        $elementName = $this->getElementName();
        return strpos($elementName, 'numbered') !== false;
    }

    /**
     * @param Context $context
     * @param Factory $elementFactory
     * @param Rule $rule
     * @param Registry $registry
     * @param CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Factory $elementFactory,
        Rule $rule,
        Registry $registry,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->elementFactory = $elementFactory;
        $this->rule = $rule;
        $this->registry = $registry;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Get rendering services
     *
     * @return array
     */
    protected function _getRenderingServices()
    {
        $configServices = $this->_getSelectedServices();

        $services = $this->_collectionFactory->create()
            ->addFieldToFilter('selectable', true)
            ->orderByIds(array_keys($configServices));

        if ($this->_getIsNumberedElement()) {
            $services->addFieldToFilter('can_numbered', true);
        }

        $renderingServices = [];
        $i = 0;
        foreach ($services as $service) {
            $serviceId = $service->getId();
            $isSelected = false;
            if (isset($configServices[$serviceId]['selected'])) {
                $isSelected = 1;
            }
            $renderingServices[] = array_merge($service->getData(), [
                'selected' => $isSelected,
                'checked' => $isSelected ? 'checked="checked"' : '',
                'sort_order' => $service->getPosition() ? $service->getPosition() : $i
            ]);
            $i++;
        }
        return $renderingServices;
    }

    /**
     * Get selected services
     *
     * @return array|SalesIds\SocialShare\Model\ResourceModel\Service\Collection
     */
    protected function _getSelectedServices()
    {
        if (!$this->_selectedServices || empty($this->_selectedServices)) {
            $widget = $this->registry->registry('current_widget_instance');
            if (!$widget) {
                return [];
            }
            $widgetParameters = $widget->getWidgetParameters();
            if (!isset($widgetParameters['conditions'])) {
                return [];
            }

            $conditions = $widgetParameters['conditions'];
            if (!isset($conditions[$this->getElementName()])) {
                $conditions[$this->getElementName()] = [];
            }
            $this->_selectedServices = $conditions[$this->getElementName()];
        }
        return $this->_selectedServices;
    }

    /**
     * Render
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $this->element = $element;

        $this->_block = $this->getLayout()
            ->createBlock('SalesIds\SocialShare\Block\Adminhtml\Widget\Services\Renderer')
            ->setElement($element)
            ->setContainer($this)
            ->setWidget($this->_widget)
            ->setServices($this->_getRenderingServices());

        return $this->toHtml();
    }

    /**
     * Get main admin element
     *
     * @return AbstractElement
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * Get service block
     *
     * @return \SalesIds\SocialShare\Block\Adminhtml\Widget\Services\Renderer
     */
    public function getBlock()
    {
        return $this->_block;
    }

    /**
     * Get rendering html id
     *
     * @return string
     */
    public function getHtmlId()
    {
        return $this->getElement()
            ->getContainer()
            ->getHtmlId();
    }

    /**
     * Get element name
     *
     * @return string
     */
    public function getElementName()
    {
        if (!$this->_elementName) {
            $parameters = str_replace('parameters', '', $this->element->getName());
            $parameters = preg_split("/\[|\]+/", $parameters, -1, PREG_SPLIT_NO_EMPTY);
            if (!empty($parameters) && isset($parameters[0])) {
                $this->_elementName = $parameters[0];
            } else {
                $this->_elementName = $this->element->getName();
            }
        }
        return $this->_elementName;
    }
}

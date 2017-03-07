<?php
/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIds\SocialShare\Block\Widget;

use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Math\Random;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Widget\Block\BlockInterface;
use Magento\Widget\Helper\Conditions as ConditionsHelper;
use SalesIds\SocialShare\Helper\Mail as MailHelper;
use SalesIds\SocialShare\Model\ResourceModel\Service\CollectionFactory;
use SalesIds\SocialShare\Model\ServiceFactory;

class ServicesWidget extends Template implements BlockInterface
{
    /**
     * Display buttons type - fixed
     *
     * @var string
     */
    const DISPLAY_TYPE_FIXED = 'fixed';

    /**
     * Display buttons type - inline
     *
     * @var string
     */
    const DISPLAY_TYPE_INLINE = 'inline';

    /**
     * Size separator
     *
     * @var string
     */
    const SIZE_SEPARATOR = 'x';

    /**
     * Position top
     *
     * @var string
     */
    const POSITION_TOP = 'top';

    /**
     * Position right
     *
     * @var string
     */
    const POSITION_RIGHT = 'right';

    /**
     * Position bottom
     *
     * @var string
     */
    const POSITION_BOTTOM = 'bottom';

    /**
     * Position left
     *
     * @var string
     */
    const POSITION_LEFT = 'left';

    /**
     * Button type counterd
     *
     * @var string
     */
    const BUTTON_TYPE_NUMBERED = 'counter';

    /**
     * Button type simple
     *
     * @var string
     */
    const BUTTON_TYPE_SIMPLE = 'simple';

    /**
     * Button style round
     *
     * @var string
     */
    const BUTTON_STYLE_ROUND = 'round';

    /**
     * Button style rounded corners
     *
     * @var string
     */
    const BUTTON_STYLE_ROUNDED = 'rounded';

    /**
     * Default button size
     *
     * @var string
     */
    const DEFAULT_BUTTON_SIZE = '50x50';

    /**
     * Default icon size width
     *
     * @var int
     */
    const DEFAULT_ICON_SIZE_WIDTH = 72;

    /**
     * Default icon size height
     *
     * @var int
     */
    const DEFAULT_ICON_SIZE_HEIGHT = 70;

    /**
     * Background position separator
     *
     * @var string
     */
    const BG_POSITION_SEPARATOR = ':';

    /**
     * Service template folder
     *
     * @var string
     */
    const SERVICES_TEMPLATE_FOLDER = 'widget/social_share/services';

    /**
     * Services delimiter
     *
     * @var string
     */
    const SERVICES_DELIMITER = ',';

    /**
     * Service type standard
     *
     * @var string
     */
    const SERVICE_TYPE_STANDARD = 'standard';

    /**
     * Service type standard
     *
     * @var string
     */
    const SERVICE_TYPE_NUMBERED = 'numbered';

    /**
     * More button service code
     *
     * @var string
     */
    const MORE_SERVICE_CODE = 'more';

    /**
     * More button service code
     *
     * @var string
     */
    const MAIL_SERVICE_CODE = 'mail';

    /**
     * Icon type width
     *
     * @var int
     */
    const ICON_TYPE_WIDTH = 0;

    /**
     * Icon type Height
     *
     * @var int
     */
    const ICON_TYPE_HEIGHT = 1;

    /**
     *
     * @var \SalesIds\SocialShare\Model\ResourceModel\Service\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var array
     */
    protected $_services = [];

    /**
     * @var array
     */
    protected $_servicesToDisplay = [];

    /**
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var EventManager
     */
    protected $_eventManager;

    /**
     * @var JsonHelper
     */
    protected $_jsonHelper;

    /**
     * Mail helper
     *
     * @var MailHelper
     */
    protected $_mailHelper;

    /**
     * @var \Magento\Widget\Helper\Conditions
     */
    protected $_conditionsHelper;

    /**
     *
     * @var ServiceFactory
     */
    protected $_serviceFactory;

    /**
     * @var ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var string
     */
    protected $_name;

    /**
     * Get is element fixed
     *
     * @return bool
     */
    protected function _getIsFixed()
    {
        return $this->getDisplayType() === self::DISPLAY_TYPE_FIXED;
    }

    /**
     * Get service template
     *
     * @param \SalesIds\SocialShare\Model\Service $service
     * @return string
     */
    protected function _getServiceTemplate($service)
    {
        $prefix = self::SERVICES_TEMPLATE_FOLDER;

        // Service type
        $serviceType = $this->getServiceType();

        // Template code
        $templateCode = $service->getCode();
        if ($service->getUrl() && !$this->_getIsNumbered()) {
            $templateCode = self::SERVICE_TYPE_STANDARD;
        }

        return sprintf('%s/%s/%s.phtml', $prefix, $serviceType, $templateCode);
    }

    /**
     * Get service block name
     *
     * @param \SalesIds\SocialShare\Model\Service $service
     * @return string
     */
    protected function _getServiceBlockName($service)
    {
        $blockClass = ucfirst($service->getCode());
        // If url was found into service, use standard rendering
        if ($service->getUrl() && !$this->_getIsNumbered()) {
            $blockClass = 'Standard';
        }
        $blockType = ucfirst($this->getServiceType());
        return sprintf('SalesIds\SocialShare\Block\Widget\Services\%s\%s', $blockType, $blockClass);
    }

    /**
     * Get is button type numbered
     *
     * @return bool
     */
    protected function _getIsNumbered()
    {
        return $this->getButtonType() === self::BUTTON_TYPE_NUMBERED;
    }

    /**
     * Get saved conditions
     *
     * @return \Magento\Rule\Model\Condition\Combine
     */
    protected function _getConditions()
    {
        $conditions = $this->getData('conditions_encoded') ?
            $this->getData('conditions_encoded') :
            $this->getData('conditions');

        if ($conditions) {
            $conditions = $this->_conditionsHelper->decode($conditions);
        }

        $conditions = $this->_filterConditions($conditions);

        if ($this->getMaxButtonsDisplayed() && $this->hasHiddenServices($conditions)) {
            $moreService = $this->_serviceFactory->create()
                ->load(self::MORE_SERVICE_CODE, 'code');

            $firstPos = null;
            $currentPos = 0;
            // Set order to the hidden services
            foreach ($conditions as $id => $service) {
                if ($firstPos === null) {
                    $firstPos = $service['position'];
                }
                if ($currentPos < $this->getMaxButtonsDisplayed()) {
                    $conditions[$id]['position'] = (string) $currentPos;
                    $currentPos++;
                    continue;
                }
                $conditions[$id]['position'] = (string) ($currentPos + 1);
                $currentPos++;
            }

            // Put at the end the more button
            $conditions[$moreService->getId()] = [
                'position' => (string) ($this->getMaxButtonsDisplayed()),
                'selected' => '1'
            ];
        }

        // Sort the collection
        uasort($conditions, function ($a, $b) {
            return $a['position'] > $b['position'];
        });

        return $conditions;
    }

    /**
     * Get border radius
     *
     * @return float
     */
    protected function _getBorderRadius()
    {
        // Corners
        switch ($this->getButtonStyle()) {
            case self::BUTTON_STYLE_ROUND:
                $radius = $this->_getMinimalSize();
                break;
            case self::BUTTON_STYLE_ROUNDED:
                $radius = $this->_getMinimalSize() * 25 / 100;
                break;
            default:
                $radius = 0;
                break;
        }
        return $radius;
    }

    /**
     * Get minimal size of button height or width
     *
     * @return int
     */
    protected function _getMinimalSize()
    {
        $size = $this->getIconWidth();
        if ($size > $this->getIconHeight()) {
            $size = $this->getIconHeight();
        }
        return $size;
    }

    /**
     * Get container size depending on icon size
     *
     * @param float $iconSize
     *
     * @return float
     */
    protected function _getIconFinalSize($size)
    {
        return floor($size * 80 / 100);
    }

    /**
     * Get icon size
     *
     * @param int $type
     * @param $defaultValue
     * @return mixed
     */
    protected function _getIconSize($type, $defaultValue)
    {
        $size = $this->_getSizes($this->_getButtonSize());
        if (!isset($size[$type])) {
            return $defaultValue;
        }
        return $size[$type];
    }

    /**
     * Get button size
     *
     * @return string
     */
    protected function _getButtonSize()
    {
        if ($this->getData('button_size')) {
            return $this->getData('button_size');
        }
        return self::DEFAULT_BUTTON_SIZE;
    }

    /**
     * Constructor
     *
     * @param Context $context
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\Math\Random $mathRandom
     * @param CollectionFactory $collectionFactory
     * @param JsonHelper $jsonHelper
     * @param ConditionsHelper $conditionsHelper
     * @param ServiceFactory $serviceFactory
     * @param MailHelper $mailHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        ManagerInterface $messageManager,
        Random $mathRandom,
        CollectionFactory $collectionFactory,
        JsonHelper $jsonHelper,
        ConditionsHelper $conditionsHelper,
        ServiceFactory $serviceFactory,
        MailHelper $mailHelper,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->eventManager = $context->getEventManager();
        $this->urlBuilder = $context->getUrlBuilder();
        $this->_messageManager = $messageManager;
        $this->_jsonHelper = $jsonHelper;
        $this->_conditionsHelper = $conditionsHelper;
        $this->_serviceFactory = $serviceFactory;
        $this->_mailHelper = $mailHelper;
        $this->_name = 'service-' . $mathRandom->getUniqueHash();
        parent::__construct($context, $data);
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Get dynamic style
     *
     * @param SalesIds\SocialShare\Block\Widget\Services\AbstractService
     *
     * @return string
     */
    public function getDynamicStyle($service)
    {
        // Dynamic parameters
        $dynamicParams = [
            'backgroundColor' => [
                'hover' => $this->getHoverButtonColor(),
                'out' => $this->getButtonColor() ? $this->getButtonColor() : $service->getColor()
            ],
            'color' => [
                'hover' => $this->getHoverIconColor(),
                'out' => $this->getIconColor() ? $this->getIconColor() : '#ffffff'
            ]
        ];

        // Define dynamic styles
        $dynamicStyles = [
            'onMouseOver' => [],
            'onMouseOut' => []
        ];
        foreach ($dynamicParams as $key => $value) {
            if (!isset($value['hover']) || !isset($value['out'])) {
                continue;
            }
            $dynamicStyles['onMouseOver'][$key] = $value['hover'];
            $dynamicStyles['onMouseOut'][$key] = $value['out'];
        }

        return $this->_arrayToParameters($dynamicStyles);
    }

    /**
     * Convert array to url parameters
     *
     * @var array $array
     * @return string
     */
    protected function _arrayToParameters($array)
    {
        // No specific rule
        if (!sizeof($array)) {
            return '';
        }

        $params = '';
        foreach ($array as $key => $values) {
            if (is_array($values)) {
                $paramValue = '';
                foreach ($values as $valueKey => $valueValue) {
                    $selector = 'this.style';
                    // Attribute color has to be applied on child link element
                    if ($valueKey == 'color') {
                        $selector = 'this.getElementsByTagName(\'A\')[0].style';
                    }
                    $paramValue .= sprintf("%s.%s='%s';", $selector, $valueKey, $valueValue);
                }
            } else {
                $paramValue = $values;
            }

            $params .= sprintf(' %s="%s"', $key, $paramValue);
        }
        return $params;
    }

    /**
     * Format string in two
     *
     * @param  string|int $str String using separator x E.g. 50x50
     * @return array(h, v)
     */
    protected function _getSizes($str)
    {
        if (strpos(self::SIZE_SEPARATOR, $str) !== false) {
            return explode(self::SIZE_SEPARATOR, $this->getButtonSpace());
        } else {
            $nb = (int) $str;
            return [$nb, $nb];
        }
        return false;
    }

    /**
     * Is element horizontal
     *
     * @return bool
     */
    public function getIsHorizontal()
    {
        if (!$this->_getIsFixed()) {
            return true;
        }
        $horizontalPositions = [
            self::POSITION_TOP,
            self::POSITION_BOTTOM
        ];
        if (in_array($this->getData('screen_position'), $horizontalPositions)) {
            return true;
        }
        return false;
    }

    /**
     * Get services
     *
     * @param bool $isHidden
     * @return \SalesIds\SocialShare\Model\ResourceModel\Service\Collection
     */
    public function getServices($isHidden = false)
    {
        if (!isset($this->_services[$isHidden])) {

            $services = $this->getServicesToDisplay();
            if ($isHidden) {
                $services   = array_slice($services, $this->getMaxButtonsDisplayed() + 1, null, true);
            }

            // No service selected
            if (empty($services)) {
                $this->_services = [];
                return $this->_services;
            }

            // Get collection of services
            $collection = $this->_collectionFactory->create()->addFieldToFilter('service_id', [
                'in' => array_keys($services)
            ]);

            if ($this->getMaxButtonsDisplayed() && !$isHidden) {
                $collection->setPageSize($this->getMaxButtonsDisplayed() + 1);
            }

            // Sorting the collection
            $collection->orderByIds(array_keys($services));

            $this->_services[$isHidden] = $collection;
        }

        return $this->_services[$isHidden];
    }

    /**
     * Get services to display
     *
     * @return array
     */
    public function getServicesToDisplay()
    {
        if (!$this->_servicesToDisplay) {
            // Getting the widget services configuration
            $this->_servicesToDisplay = $this->_getConditions();
        }

        return $this->_servicesToDisplay;
    }

    /**
     * Filter conditions
     *
     * @param array $conditions
     * @return array
     */
    protected function _filterConditions($conditions)
    {
        $fieldName = $this->_getIsNumbered() ? 'services_numbered' : 'services';
        $services = isset($conditions[$fieldName]) ? $conditions[$fieldName] : [];

        if (empty($services)) {
            return [];
        }

        $mailService = $this->_serviceFactory->create()
            ->load(self::MAIL_SERVICE_CODE, 'code');

        // Remove unused services
        foreach ($services as $key => $service) {
            if (!isset($service['selected']) || $service['selected'] !== '1') {
                unset($services[$key]);
            }

            // Do not display mail service if cannot be displayed
            if ($mailService && $mailService->getId() &&
                $key == $mailService->getId() && !$this->_mailHelper->isAllowDisplay()
            ) {
                unset($services[$key]);
            }
        }

        return $services;
    }

    /**
     * has current tool hidden services
     *
     * @param array $conditions
     * @return boolean
     */
    public function hasHiddenServices($conditions = null)
    {
        if (!$conditions) {
            $services = $this->getServicesToDisplay();
        } else {
            $services = $conditions;
        }
        if (!$services) {
            return false;
        }
        return $this->getMaxButtonsDisplayed() < count($services);
    }

    /**
     * Get hidden services that have not to be displayed into the toolbar
     *
     * @return \SalesIds\SocialShare\Model\ResourceModel\Service\Collection
     */
    public function getHiddenServices()
    {
        return $this->getServices(true);
    }

    /**
     * Get service type
     *
     * @return string
     */
    public function getServiceType()
    {
        $serviceType = self::SERVICE_TYPE_STANDARD;
        if ($this->_getIsNumbered()) {
            $serviceType = self::SERVICE_TYPE_NUMBERED;
        }
        return $serviceType;
    }

    /**
     * Retrieve display type for products
     *
     * @return string
     */
    public function getDisplayType()
    {
        if (!$this->hasData('display_type')) {
            $this->setData('display_type', self::DISPLAY_TYPE_INLINE);
        }
        return $this->getData('display_type');
    }

    /**
     * Get screen position
     *
     * @return string
     */
    public function getScreenPosition()
    {
        if (!$this->_getIsFixed()) {
            return '';
        }
        return ' ' . $this->getData('screen_position');
    }

    /**
     * Get rendering services
     *
     * @param \SalesIds\SocialShare\Model\ResourceModel\Service\Collection $services
     * @return array
     */
    public function getRenderingServices($services = null)
    {
        if (!$services) {
            $services = $this->getServices();
        }

        $renderingServices = [];
        foreach ($services as $service) {
            // Load bloc for specific service
            try {
                $renderingService = $this->getLayout()
                    ->createBlock($this->_getServiceBlockName($service))
                    ->setData($service->getData())
                    ->setServiceUrl($service->getUrl())
                    ->setWidget($this)
                    ->setTemplate($this->_getServiceTemplate($service));

                if (!$renderingService->canDisplay()) {
                    continue;
                }
                $renderingServices[$service->getCode()] = $renderingService;
            } catch (\Exception $e) {
                $template = $this->_getServiceTemplate($service);
                $name = $service->getName();
                $this->_messageManager->addErrorMessage(
                    __('Template %1 for service %2 not found.', $template, $name)
                );
            }
        }

        return $renderingServices;
    }

    /**
     * Get bar style
     *
     * @return string
     */
    public function getBarStyle()
    {
        $style = '';

        if (!$this->_getIsFixed()) {
            return $style;
        }

        // Border shifting
        if ($this->getBorderShift()) {
            $shift = $this->getBorderShift();
            if ($this->getIsHorizontal()) {
                $style .= sprintf('left:%s%%;', $shift);
            } else {
                $style .= sprintf('top:%s%%;', $shift);
            }
        }

        return $style;
    }

    /**
     * Get service container style
     *
     * @param \SalesIds\SocialShare\Model\Service $service
     * @return string
     */
    public function getServiceContainerStyle($service)
    {
        $style = '';

        // No background, border or size for numbered services
        if (!$this->_getIsNumbered()) {
            $style = sprintf('border-radius:%spx; height:%spx; width:%spx;',
                $this->_getBorderRadius(),
                $this->getContainerHeight(),
                $this->getContainerWidth()
            );

            $style .= sprintf('background-color:%s;',
                $this->getButtonColor() ? $this->getButtonColor() : $service->getColor()
            );
        }

        // Space between buttons
        if ($this->getButtonSpace()) {
            list($h, $v) = $this->_getSizes($this->getButtonSpace());
            $style .= sprintf('margin:%spx %spx %spx %spx;', $v/2, $h/2, $v/2, $h/2);
        }

        return $style;
    }

    /**
     * Get service container class name
     *
     * @var \SalesIds\SocialShare\Model\ResourceModel\Service\Collection $collection
     * @param \SalesIds\SocialShare\Model\Service $service
     * @var int $iterator
     * @return string
     */
    public function getServiceContainerClass($collection, $service, $iterator)
    {
        $className = 'service-item ' . $service->getCode();

        // Is first item
        if ($iterator == 0) {
            $className .= ' first';
        }

        if ($this->_getIsNumbered()) {
            if ($service->isDisplayCounterTop()) {
                $className .= ' above';
            } else {
                $className .= ' beside';
            }
        } else {
            $className .= ' ' . $this->getButtonStyle();
        }

        // Is last item
        if ($iterator == count($collection) - 1) {
            $className .= ' last';
        }

        if ($this->getIsHoverEffect()) {
            $className .= ' hover-effect';
        }

        return $className;
    }

    /**
     * Get icon container height
     *
     * @return int
     */
    public function getContainerHeight()
    {
        return $this->_getIconSize(
            self::ICON_TYPE_HEIGHT,
            self::DEFAULT_ICON_SIZE_HEIGHT
        );
    }

    /**
     * Get icon container width
     *
     * @return int
     */
    public function getContainerWidth()
    {
        return $this->_getIconSize(
            self::ICON_TYPE_WIDTH,
            self::DEFAULT_ICON_SIZE_WIDTH
        );
    }

    /**
     * Get icon height
     *
     * @return int
     */
    public function getIconHeight()
    {
        $height = $this->getContainerHeight();
        return $this->_getIconFinalSize($height);
    }

    /**
     * Get icon width
     *
     * @return int
     */
    public function getIconWidth()
    {
        $width = $this->getContainerWidth();
        return $this->_getIconFinalSize($width);
    }
}

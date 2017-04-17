<?php
/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIds\SocialShare\Model;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DB\MapperInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Option\ArrayInterface;
use Magento\Framework\Registry;
use SalesIds\SocialShare\Api\Data\ServiceInterface;

class Service extends AbstractModel implements ServiceInterface, ArrayInterface
{
    /**
     * Request
     *
     * @var RequestInterface
     */
    protected $_request;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'salesids_socialshare_service';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('SalesIds\SocialShare\Model\ResourceModel\Service');
    }

    /**
     * Initialize dependencies
     *
     * @param Context $context
     * @param Registry $registry
     * @param RequestInterface $request
     * @param AbstractResource $resource = null
     * @param AbstractDb $resourceCollection
     * @param array $data
     * @return void
     */
    public function __construct(
        Context $context,
        Registry $registry,
        RequestInterface $request,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_request = $request;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Get id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::SERVICE_ID);
    }

    /**
     * Get code
     *
     * @return string|null
     */
    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * Get url
     *
     * @return string|null
     */
    public function getUrl()
    {
        return $this->getData(self::URL);
    }

    /**
     * Get color
     *
     * @return string|null
     */
    public function getColor()
    {
        return $this->getData(self::COLOR);
    }

    /**
     * Get icon
     *
     * @return string|null
     */
    public function getIcon()
    {
        return $this->getData(self::ICON);
    }

    /**
     * Get can service be numbered
     *
     * @return bool|null
     */
    public function getCanNumbered()
    {
        return $this->getData(self::CAN_NUMBERED);
    }

    /**
     * Set id
     *
     * @param int $id
     * @return \SalesIds\SocialShare\Api\Data\ServiceInterface
     */
    public function setId($id)
    {
        return $this->setData(self::SERVICE_ID, $id);
    }

    /**
     * Set code
     *
     * @param string $code
     * @return \SalesIds\SocialShare\Api\Data\ServiceInterface
     */
    public function setCode($code)
    {
        return $this->setData(self::CODE, $code);
    }

    /**
     * Set name
     *
     * @param string $name
     * @return \SalesIds\SocialShare\Api\Data\ServiceInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Set url
     *
     * @param string $url
     * @return \SalesIds\SocialShare\Api\Data\ServiceInterface
     */
    public function setUrl($url)
    {
        return $this->setData(self::URL, $url);
    }

    /**
     * Set color
     *
     * @param string $color
     * @return \SalesIds\SocialShare\Api\Data\ServiceInterface
     */
    public function setColor($color)
    {
        return $this->setData(self::COLOR, $color);
    }

    /**
     * Set icon
     *
     * @param string $icon
     * @return \SalesIds\SocialShare\Api\Data\ServiceInterface
     */
    public function setIcon($icon)
    {
        return $this->setData(self::ICON, $icon);
    }

    /**
     * Set can service be numbered
     *
     * @param int $canNumbered
     * @return \SalesIds\SocialShare\Api\Data\ServiceInterface
     */
    public function setCanNumbered($canNumbered)
    {
        return $this->setData(self::CAN_NUMBERED, $canNumbered);
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $collection = $this->getCollection()
            ->setOrder('name', MapperInterface::SORT_ORDER_ASC);

        $options = [];
        foreach ($collection as $item) {
            $options[] = [
                'value' => $item->getId(),
                'label' => $item->getName(),
            ];
        }
        return $options;
    }
}

<?php
/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIds\SocialShare\Api\Data;

interface ServiceInterface
{
    /**
     * Constants for keys of data array
     * Identical to the name of the getter in snake case
     */
    const SERVICE_ID   = 'service_id';
    const CODE         = 'code';
    const NAME         = 'name';
    const URL          = 'url';
    const COLOR        = 'color';
    const ICON         = 'icon';
    const CAN_NUMBERED = 'can_numbered';

    /**
     * Get id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get code
     *
     * @return string|null
     */
    public function getCode();

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName();

    /**
     * Get url
     *
     * @return string|null
     */
    public function getUrl();

    /**
     * Get color
     *
     * @return string|null
     */
    public function getColor();

    /**
     * Get icon
     *
     * @return string|null
     */
    public function getIcon();

    /**
     * Get can service be numbered
     *
     * @return bool|null
     */
    public function getCanNumbered();

    /**
     * Set id
     *
     * @param int $id
     * @return \SalesIds\SocialShare\Api\Data\ServiceInterface
     */
    public function setId($id);

    /**
     * Set code
     *
     * @param string $code
     * @return \SalesIds\SocialShare\Api\Data\ServiceInterface
     */
    public function setCode($code);

    /**
     * Set name
     *
     * @param string $name
     * @return \SalesIds\SocialShare\Api\Data\ServiceInterface
     */
    public function setName($name);

    /**
     * Set url
     *
     * @param string $url
     * @return \SalesIds\SocialShare\Api\Data\ServiceInterface
     */
    public function setUrl($url);

    /**
     * Set color
     *
     * @param string $color
     * @return \SalesIds\SocialShare\Api\Data\ServiceInterface
     */
    public function setColor($color);

    /**
     * Set icon
     *
     * @param string $icon
     * @return \SalesIds\SocialShare\Api\Data\ServiceInterface
     */
    public function setIcon($icon);

    /**
     * Set can service be numbered
     *
     * @param int $canNumbered
     * @return \SalesIds\SocialShare\Api\Data\ServiceInterface
     */
    public function setCanNumbered($canNumbered);
}

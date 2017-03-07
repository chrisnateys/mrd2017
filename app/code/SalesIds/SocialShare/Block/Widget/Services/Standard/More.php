<?php
/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIds\SocialShare\Block\Widget\Services\Standard;

class More extends Standard
{
    /**
     * More button class name
     *
     * @var string
     */
    const MORE_BUTTON_CLASS = 'more-button';

    /**
     * More button class name
     *
     * @var string
     */
    const MORE_MODAL_CLASS = 'more-modal';

    /**
     * Get button class name
     *
     * @return string
     */
    public function getClass()
    {
        return sprintf('%s %s', parent::getClass (), self::MORE_BUTTON_CLASS);
    }

    /**
     * Get link id
     *
     * @return string
     */
    public function getLinkId()
    {
        return sprintf('%s%s', self::MORE_BUTTON_CLASS, $this->getWidget()->getName());
    }

    /**
     * Get modal box id
     *
     * @return string
     */
    public function getModalId()
    {
        return sprintf('%s%s', self::MORE_MODAL_CLASS, $this->getWidget()->getName());
    }

    /**
     * Get hidden services to display into the modal box
     *
     * @return array
     */
    public function getHiddenRenderingServices()
    {
        $services = $this->getWidget()->getHiddenServices();
        return $this->getWidget()->getRenderingServices($services);
    }
}

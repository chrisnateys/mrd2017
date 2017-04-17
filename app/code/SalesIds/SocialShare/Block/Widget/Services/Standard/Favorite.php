<?php
/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIds\SocialShare\Block\Widget\Services\Standard;

class Favorite extends Standard
{
    /**
     * Mail button class name
     *
     * @var string
     */
    const FAVORITE_BUTTON_CLASS = 'favorite-button';

    /**
     * Get link id
     *
     * @return string
     */
    public function getLinkId()
    {
        return sprintf('%s%s', self::FAVORITE_BUTTON_CLASS, $this->getWidget()->getName());
    }
}

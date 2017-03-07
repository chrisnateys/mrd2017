<?php
/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIds\SocialShare\Block\Widget\Services\Numbered;

use SalesIds\SocialShare\Block\Widget\Services\AbstractService;

class Facebook extends AbstractService
{
    /**
     * Type top countered button
     * @var string
     */
    protected $_typeNumberedTop = 'box_count';

    /**
     * Type above countered button
     * @var string
     */
    protected $_typeNumberedBeside = 'button_count';

    /**
     * Get service script src
     *
     * @return string
     */
    public function getSrc()
    {
        return '//connect.facebook.net/fr_FR/sdk.js#xfbml=1&version=v2.5';
    }

    /**
     * Get service URL
     *
     * @return string
     */
    public function getServiceUrl()
    {
        return $this->getCurrentUrl();
    }
}

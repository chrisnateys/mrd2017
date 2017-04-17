<?php
/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIds\SocialShare\Block\Widget\Services\Numbered;

use SalesIds\SocialShare\Block\Widget\Services\AbstractService;

class Pinterest extends AbstractService
{
    /**
     * Type top countered button
     * @var string
     */
    protected $_typeNumberedTop = 'above';

    /**
     * Type above countered button
     * @var string
     */
    protected $_typeNumberedBeside = 'beside';

    /**
     * Get service URL
     *
     * @return string
     */
    public function getServiceUrl()
    {
        return sprintf(
            'https://www.pinterest.com/pin/create/button/?url=%s',
            urlencode($this->getCurrentUrl())
        );
    }

    /**
     * Is button tall
     *
     * @return bool
     */
    public function isTall()
    {
        return $this->getNumberedType() == $this->_typeNumberedTop;
    }
}

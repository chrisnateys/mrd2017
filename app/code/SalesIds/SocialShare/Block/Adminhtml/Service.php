<?php
/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIds\SocialShare\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

class Service extends Container
{
    /**
     * Block constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->buttonList->update('add', 'label', __('Add New Service'));
    }
}

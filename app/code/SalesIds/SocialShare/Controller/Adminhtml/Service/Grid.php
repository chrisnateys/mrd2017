<?php
/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIds\SocialShare\Controller\Adminhtml\Service;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use SalesIds\SocialShare\Helper\Data as DataHelper;

class Grid extends Action
{
    /**
     * Check is allowed access
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(DataHelper::ACL_SERVICE);
    }

    /**
     * Initialize dependencies
     *
     * @param Context $context
     * @param DataHelper $dataHelper
     * @return void
     */
    public function __construct(
        Context $context,
        DataHelper $dataHelper
    ) {
        $this->_dataHelper = $dataHelper;
        parent::__construct($context);
    }

    /**
     * Services grid
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }
}

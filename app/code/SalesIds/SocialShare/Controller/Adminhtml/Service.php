<?php
/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIds\SocialShare\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use SalesIds\SocialShare\Helper\Data as DataHelper;

abstract class Service extends Action
{
    /**
     * @var DataHelper
     */
    protected $_dataHelper;

    /**
     * @var FileFactory
     */
    protected $_fileFactory;

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
     * @param FileFactory $fileFactory
     * @return void
     */
    public function __construct(
        Context $context,
        DataHelper $dataHelper,
        FileFactory $fileFactory
    ) {
        $this->_dataHelper = $dataHelper;
        $this->_fileFactory = $fileFactory;
        parent::__construct($context);
    }
}

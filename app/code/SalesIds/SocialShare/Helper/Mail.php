<?php
/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIds\SocialShare\Helper;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Mail extends AbstractHelper
{
    /**
     * XML paths
     *
     * @var string
     */
    const XML_PATH_ALLOW_FOR_GUEST = 'socialshare/email/allow_guest';
    const XML_PATH_EMAIL_TEMPLATE = 'socialshare/email/email_template';

    /**
     * Customer session
     *
     * @var CustomerSession
     */
    protected $_customerSession;

    /**
     * @param Context $context
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession
    ) {
        $this->_customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * Retrieve Email Template
     *
     * @param int $store
     * @return mixed
     */
    public function getEmailTemplate($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Check allow send email for guest
     *
     * @param int $store
     * @return bool
     */
    public function isAllowDisplay($store = null)
    {
       if ($this->_customerSession->isLoggedIn()) {
           return true;
       }
       return $this->scopeConfig->isSetFlag(
           self::XML_PATH_ALLOW_FOR_GUEST,
           ScopeInterface::SCOPE_STORE,
           $store
       );
    }
}

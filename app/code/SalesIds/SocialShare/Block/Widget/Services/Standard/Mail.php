<?php
/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIds\SocialShare\Block\Widget\Services\Standard;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\View\Element\Template\Context;
use SalesIds\SocialShare\Helper\Data as DataHelper;
use SalesIds\SocialShare\Helper\Mail as MailHelper;
use SalesIds\SocialShare\Helper\ReCaptcha as ReCaptchaHelper;

class Mail extends Standard
{
    /**
     * Sender email config path
     *
     * @var string
     */
    const XML_PATH_GUEST_EMAIL_SENDER = 'socialshare/email/guest_email_sender';

    /**
     * Email subject config path
     */
    const XML_PATH_EMAIL_SUBJECT = 'socialshare/email/email_subject';

    /**
     * Email body config path
     *
     * @var string
     */
    const XML_PATH_EMAIL_BODY = 'socialshare/email/email_body';

    /**
     * Mail button class name
     *
     * @var string
     */
    const MAIL_BUTTON_CLASS = 'mail-button';

    /**
     * Mail modal class name
     *
     * @var string
     */
    const MAIL_MODAL_CLASS = 'mail-modal';

    /**
     * Data helper
     *
     * @var DataHelper
     */
    protected $_dataHelper;

    /**
     * Mail helper
     *
     * @var MailHelper
     */
    protected $_mailHelper;

    /**
     * @var ReCaptchaHelper
     */
    protected $_reCaptchaHelper;

    /**
     * Customer session
     *
     * @var CustomerSession
     */
    protected $_customerSession;

    /**
     * Scope config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Errors
     *
     * @var array
     */
    protected $_errors = [];

    /**
     * Get mailto subject
     *
     * @return string
     */
    protected function _getSubject()
    {
        return __($this->_dataHelper->getEmailSubject());
    }

    /**
     * Get mailto body
     *
     * @return string
     */
    protected function _getBody()
    {
        return __($this->_dataHelper->getEmailBody(), $this->_urlBuilder->getCurrentUrl());
    }

    /**
     * Add an error
     *
     * @param string $error
     * @return null
     */
    protected function _addError($error)
    {
        $this->_errors[] = $error;
    }

    /**
     * Constructor
     *
     * @param Context $context
     * @param DataHelper $dataHelper
     * @param MailHelper $mailHelper
     * @param ReCaptchaHelper $reCaptchaHelper
     * @param CustomerSession $customerSession
     * @param array $data
     */
    public function __construct(
        Context $context,
        DataHelper $dataHelper,
        MailHelper $mailHelper,
        ReCaptchaHelper $reCaptchaHelper,
        CustomerSession $customerSession,
        array $data = []
    ) {
        $this->_dataHelper = $dataHelper;
        $this->_mailHelper = $mailHelper;
        $this->_reCaptchaHelper = $reCaptchaHelper;
        $this->_customerSession = $customerSession;
        $this->_scopeConfig = $context->getScopeConfig();
        if ($reCaptchaHelper->isMissconfigured()) {
            $this->_addError(__('reCAPTCHA key has not been configured. Please configure it into "Stores > Configuration > General > Social Share > reCAPTCHA" section from backend.'));
        }
        parent::__construct($context, $dataHelper, $data);
    }

    /**
     * Has an error
     *
     * @return bool
     */
    public function hasError()
    {
        return count($this->_errors) > 0;
    }

    /**
     * Get errors
     *
     * @return mixed
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Get link id
     *
     * @return string
     */
    public function getLinkId()
    {
        return sprintf('%s%s', self::MAIL_BUTTON_CLASS, $this->getWidget()->getName());
    }

    /**
     * Is current session logged in
     *
     * @return bool
     */
    public function getIsLoggedIn()
    {
        return $this->_customerSession->isLoggedIn();
    }

    /**
     * Get link id
     *
     * @return string
     */
    public function getModalId()
    {
        return sprintf('%s-%s', self::MAIL_MODAL_CLASS, $this->getWidget()->getName());
    }

    /**
     * Can display
     *
     * @return boolean
     */
    public function canDisplay()
    {
        return $this->_mailHelper->isAllowDisplay();
    }

    /**
     * Get form action
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->_urlBuilder->getUrl('sisocialshare/share_mail/send/');
    }

    /**
     * Get form key
     *
     * @return string
     */
    public function getFormKey()
    {
        return $this->_customerSession->getData(FormKey::FORM_KEY);
    }

    /**
     * Get from email
     *
     * @return string
     */
    public function getFromEmail()
    {
        if ($this->getIsLoggedIn()) {
            return $this->_customerSession->getCustomer()->getEmail();
        }
        return $this->_scopeConfig->getValue(
            self::XML_PATH_GUEST_EMAIL_SENDER,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get default email subject
     *
     * @return string
     */
    public function getSubject()
    {
        $subject = __($this->_scopeConfig->getValue(
            self::XML_PATH_EMAIL_SUBJECT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ));
        return $this->_dataHelper->replaceTags($subject, $this->getWidget());
    }

    /**
     * Get default email body
     *
     * @return string
     */
    public function getBody()
    {
        $body = __($this->_scopeConfig->getValue(
            self::XML_PATH_EMAIL_BODY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ));
        return $this->_dataHelper->replaceTags($body, $this->getWidget());
    }

    /**
     * Get captcha helper
     *
     * @return string
     */
    public function getCaptchaHelper()
    {
        return $this->_reCaptchaHelper;
    }
}

<?php
/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIds\SocialShare\Controller\Share\Mail;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use SalesIds\SocialShare\Helper\Ajax as AjaxHelper;
use SalesIds\SocialShare\Helper\ReCaptcha as ReCaptchaHelper;
use SalesIds\SocialShare\Model\Service\Standard\Mail\SendMail;

class Send extends Action
{
    /**
     * Sender email config path
     */
    const XML_PATH_GUEST_EMAIL_ALLOWED = 'socialshare/email/allow_guest';

    /**
     * Sender email config path
     */
    const XML_PATH_GUEST_EMAIL_SENDER = 'socialshare/email/guest_email_sender';

    /**
     * @var CustomerSession
     */
    protected $_customerSession;

    /**
     * @var Validator
     */
    protected $_formKeyValidator;

    /**
     * @var AjaxHelper
     */
    protected $_ajaxHelper;

    /**
     * @var ReCaptchaHelper
     */
    protected $_reCaptchaHelper;

    /**
     * @var SendMail
     */
    protected $_sendMail;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Mail\Template\SenderResolverInterface
     */
    protected $_senderResolver;

    /**
     * Get sender email
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return string
     */
    protected function _getSender($request)
    {
        if ($this->_getIsLoggedIn()) {
            $result = [
                'email' => $this->_customerSession->getCustomer()->getEmail(),
                'name' => $this->_customerSession->getCustomer()->getName()
            ];
        } else {
            $from = $this->_getDefaultFromEmail();
            $result = $this->_senderResolver->resolve($from);
        }
        $result['subject'] = $request->getPost('subject');
        $result['message'] = $request->getPost('body');
        return $result;
    }

    /**
     * Is current session logged in
     *
     * @return bool
     */
    protected function _getIsLoggedIn()
    {
        return $this->_customerSession->isLoggedIn();
    }

    /**
     * Get from email
     *
     * @return string
     */
    protected function _getDefaultFromEmail()
    {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_GUEST_EMAIL_SENDER,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Can e-mail be sent
     *
     * @return bool
     */
    protected function _canSend()
    {
        $guestAllowed = $this->_scopeConfig->getValue(
            self::XML_PATH_GUEST_EMAIL_ALLOWED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($guestAllowed) {
            return true;
        }
        if ($this->_getIsLoggedIn()) {
            return true;
        }
        return false;
    }

    /**
     * Validate posted form
     *
     * @return bool
     */
    protected function _validateForm()
    {
        $requiredFields = ['to_email', 'subject', 'body'];
        foreach ($requiredFields as $requiredField) {
            if (!$this->getRequest()->getParam($requiredField)) {
                return false;
            }
        }
        if (!$this->_validateCaptcha()) {
            return false;
        }
        return true;
    }

    /**
     * Validate captcha
     *
     * @return bool
     */
    protected function _validateCaptcha()
    {
        if (!$this->_reCaptchaHelper->isEnabled()) {
            return true;
        }
        $captchaResponse = $this->getRequest()->getParam('g-recaptcha-response');
        if (!$captchaResponse) {
            return false;
        }
        return $this->_reCaptchaHelper->verifyResponse($captchaResponse);
    }

    /**
     * @param Context $context
     * @param Validator $formKeyValidator
     * @param AjaxHelper $ajaxHelper
     * @param ReCaptchaHelper $reCaptchaHelper
     * @param CustomerSession $customerSession
     * @param SendMail $sendMail
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        Validator $formKeyValidator,
        AjaxHelper $ajaxHelper,
        ReCaptchaHelper $reCaptchaHelper,
        CustomerSession $customerSession,
        SendMail $sendMail,
        ScopeConfigInterface $scopeConfig,
        SenderResolverInterface $senderResolver
    ) {
        $this->_formKeyValidator = $formKeyValidator;
        $this->_ajaxHelper = $ajaxHelper;
        $this->_reCaptchaHelper = $reCaptchaHelper;
        $this->_customerSession = $customerSession;
        $this->_sendMail = $sendMail;
        $this->_scopeConfig = $scopeConfig;
        $this->_senderResolver = $senderResolver;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->_canSend()) {
            return $this->_ajaxHelper->respondError(__('This action is not allowed.'));
        }

        $request = $this->getRequest();
        if (!$this->_formKeyValidator->validate($request)) {
            return $this->_ajaxHelper->respondError(__('Invalid Form Key. Please refresh the page.'));
        }

        if (!$this->_validateForm()) {
            if (!$this->_reCaptchaHelper->isEnabled()) {
                return $this->_ajaxHelper->respondError(__('Invalid Form Data. Please fill all required fields.'));
            } else {
                return $this->_ajaxHelper->respondError(__('Invalid Form Data. Please fill all required fields and validate the captcha.'));
            }
        }

        $this->_sendMail->setSender($this->_getSender($request));
        $this->_sendMail->setRecipient($this->getRequest()->getPost('to_email'));

        try {
            $validate = $this->_sendMail->validate();
            if ($validate === true) {
                $this->_sendMail->send();
                return $this->_ajaxHelper->respondSuccess(__('Your messsage has been sent successfully'));
            } else {
                return $this->_ajaxHelper->respondError($validate);
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return $this->_ajaxHelper->respondError($e->getMessage());
        } catch (\Exception $e) {
            return $this->_ajaxHelper->respondError($e->getMessage());
        }
    }
}

<?php
/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIds\SocialShare\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class ReCaptcha extends AbstractHelper
{
    /**
     * Signup url
     *
     * @var string
     */
    CONST SIGNUP_URL = "https://www.google.com/recaptcha/admin";

    /**
     * Captcha is enabled
     *
     * @var string
     */
    const XML_PATH_RECAPTCHA_ENABLED = 'socialshare/recaptcha/enabled';

    /**
     * Site verify url
     *
     * @var string
     */
    CONST SITE_VERIFY_URL = "https://www.google.com/recaptcha/api/siteverify?";

    /**
     * Captcha public key
     *
     * @var string
     */
    const XML_PATH_RECAPTCHA_PUBLIC_KEY = 'socialshare/recaptcha/public_key';

    /**
     * Captcha secret key
     *
     * @var string
     */
    const XML_PATH_RECAPTCHA_SECRET_KEY = 'socialshare/recaptcha/secret_key';

    /**
     * PHP version
     *
     * @var string
     */
    CONST VERSION = 'php_1.0';

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_requestHttp;

    /**
     * Encodes the given data into a query string format.
     *
     * @param array $data
     * @return string
     */
    protected function _encodeQS($data)
    {
        $req = '';
        foreach ($data as $key => $value) {
            $req .= $key . '=' . urlencode(stripslashes($value)) . '&';
        }
        // Cut the last '&'
        $req=substr($req, 0, strlen($req)-1);
        return $req;
    }

    /**
     * Submits an HTTP GET to a reCAPTCHA server.
     *
     * @param string $path
     * @param array  $data
     * @return array response
     */
    protected function _submitHTTPGet($path, $data)
    {
        $req = $this->_encodeQS($data);
        $response = file_get_contents($path . $req);
        return $response;
    }

    /**
     * Get secret key
     *
     * @return string
     */
    protected function _getSecretKey()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_RECAPTCHA_SECRET_KEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get remote Ip
     *
     * @return string
     */
    protected function _getRemoteIp()
    {
        return $this->_requestHttp->getClientIp(false);
    }

    /**
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        $this->_requestHttp = $context->getRequest();
        parent::__construct($context);
    }

    /**
     * Is ReCaptcha enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_RECAPTCHA_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Is ReCaptcha missconfigured
     *
     * @return bool
     */
    public function isMissconfigured()
    {
        if (!$this->isEnabled()) {
            return false;
        }
        if ($this->_getSecretKey() && $this->getPublicKey()) {
            return false;
        }
        return true;
    }

    /**
     * Get captcha public key
     *
     * @return string
     */
    public function getPublicKey()
    {
        if (!$this->isEnabled()) {
            return '';
        }
        return $this->scopeConfig->getValue(
            self::XML_PATH_RECAPTCHA_PUBLIC_KEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Calls the reCAPTCHA siteverify API to verify whether the user passes
     * CAPTCHA test.
     *
     * @param string $response
     * @return DataObject
     */
    public function verifyResponse($response)
    {
        $recaptchaResponse = new \Magento\Framework\DataObject();

        // Verify secret key
        $secretKey = $this->_getSecretKey();
        if (!$secretKey) {
            $recaptchaResponse->setData([
                'success' => false,
                'error_codes' => 'missing-secret'
            ]);
            return $recaptchaResponse;
        }

        // Discard empty solution submissions
        if ($response == null || strlen($response) == 0) {
            $recaptchaResponse->setData([
                'success' => false,
                'error_codes' => 'missing-input'
            ]);
            return $recaptchaResponse;
        }

        $getResponse = $this->_submitHttpGet(
            self::SITE_VERIFY_URL,
            array (
                'secret' => $secretKey,
                'remoteip' => $this->_getRemoteIp(),
                'v' => self::VERSION,
                'response' => $response
            )
        );

        $answers = json_decode($getResponse, true);

        if (trim($answers['success']) == true) {
            $recaptchaResponse->setSuccess(true);
        } else {
            $errorCodes = isset($answers['error-codes']) ? $answers['error-codes'] : '';
            $recaptchaResponse->setSuccess(false)
                ->setErrorCodes($errorCodes);
        }
        return $recaptchaResponse;
    }
}


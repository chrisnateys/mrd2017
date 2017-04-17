<?php
/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIds\SocialShare\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class Ajax extends AbstractHelper
{
    /**
     * @var JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * Get Json from array
     *
     * @param array $data
     * @return string
     */
    protected function getJson($data)
    {
        $resultJson = $this->_resultJsonFactory->create();
        $resultJson->setHeader('Content-type', 'application/json', true);
        $resultJson->setData($data);
        return $resultJson;
    }

    /**
     * Format messages for response
     *
     * @param string|array $data
     * @return array
     */
    protected function _formatMessages($messages)
    {
        if (!is_array($messages)) {
            $messages = [$messages];
        }
        $data = [
            'messages' => $messages
        ];
        return $data;
    }

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory
    ) {
        $this->_resultJsonFactory = $resultJsonFactory;
        return parent::__construct($context);
    }

    /**
     * Respond success json
     *
     * @param string|array $messages
     * @return string
     */
    public function respondSuccess($messages)
    {
        $data = [
            'success' => true,
            'data' => $this->_formatMessages($messages)
        ];
        return $this->getJson($data);
    }

    /**
     * Respond error json
     *
     * @param string|array $messages
     * @return string
     */
    public function respondError($messages)
    {
        $data = [
            'success' => false,
            'data' => $this->_formatMessages($messages)
        ];
        return $this->getJson($data);
    }
}

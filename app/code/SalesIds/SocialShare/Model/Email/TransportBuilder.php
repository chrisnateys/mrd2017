<?php
/**
 * Mail Template Transport Builder
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIds\SocialShare\Model\Email;

use Magento\Email\Model\AbstractTemplate;
use Magento\Framework\App\TemplateTypesInterface;
use Magento\Framework\Mail\MessageInterface;

class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
    /**
     * Template data
     *
     * @var array
     */
    protected $templateData = [];

    /**
     * Set template data
     *
     * @param array $data
     * @return $this
     */
    public function setTemplateData($data)
    {
        $this->templateData = $data;
        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function prepareMessage()
    {
        /** @var AbstractTemplate $template */
        $template = $this->getTemplate()->addData($this->templateData);
        $types = [
            TemplateTypesInterface::TYPE_TEXT => MessageInterface::TYPE_TEXT,
            TemplateTypesInterface::TYPE_HTML => MessageInterface::TYPE_HTML,
        ];

        $body = $template->processTemplate();
        $subject = isset($this->templateData['subject']) ? $this->templateData['subject'] : $template->getSubject();
        $this->message->setMessageType($types[$template->getType()])
            ->setBody($body)
            ->setSubject($subject);
        return $this;
    }
}

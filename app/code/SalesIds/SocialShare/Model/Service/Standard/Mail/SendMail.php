<?php
/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIds\SocialShare\Model\Service\Standard\Mail;

use Magento\Framework\Exception\LocalizedException as CoreException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use SalesIds\SocialShare\Helper\Mail as MailHelper;
use SalesIds\SocialShare\Model\Email\TransportBuilder;

class SendMail extends AbstractModel
{
    /**
     * Max recipients allowed
     *
     * @var int
     */
    const MAX_RECIPIENTS = 1;

    /**
     * Recipient Names
     *
     * @var array
     */
    protected $_names = [];

    /**
     * Recipient Emails
     *
     * @var array
     */
    protected $_emails = [];

    /**
     * Sender data array
     *
     * @var \Magento\Framework\DataObject|array
     */
    protected $_sender = [];

    /**
     * Count of sent in last period
     *
     * @var int
     */
    protected $_sentCount;

    /**
     * Last values for Cookie
     *
     * @var string
     */
    protected $_lastCookieValue = [];

    /**
     * Mail helper
     *
     * @var
     */
    protected $_mailHelper = null;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $remoteAddress;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param StoreManagerInterface $storeManager
     * @param TransportBuilder $transportBuilder
     * @param MailHelper $mailHelper
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Registry $registry,
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder,
        MailHelper $mailHelper,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        $this->_transportBuilder = $transportBuilder;
        $this->_mailHelper = $mailHelper;
        $this->_escaper = $escaper;
        $this->remoteAddress = $remoteAddress;
        $this->cookieManager = $cookieManager;
        $this->inlineTranslation = $inlineTranslation;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @return $this
     * @throws CoreException
     */
    public function send()
    {
        $this->inlineTranslation->suspend();

        $message = nl2br($this->getSender()->getMessage());
        $subject = $this->_escaper->escapeHtml($this->getSender()->getSubject());

        $sender = [
            'name' => $this->_escaper->escapeHtml($this->getSender()->getName()),
            'email' => $this->_escaper->escapeHtml($this->getSender()->getEmail()),
        ];

        foreach ($this->getRecipients()->getEmails() as $k => $email) {
            $name = $this->getRecipients()->getNames($k);

            $this->_transportBuilder->setTemplateIdentifier(
                $this->_mailHelper->getEmailTemplate()
            )->setTemplateOptions([
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $this->_storeManager->getStore()->getId(),
            ])->setFrom(
                $sender
            )->setTemplateVars([
                'name' => $name,
                'email' => $email,
                'message' => $message,
                'sender_name' => $sender['name'],
                'sender_email' => $sender['email']
            ])->setTemplateData([
                'subject' => $subject
            ])->addTo(
                $email,
                $name
            );

            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();
        }

        $this->inlineTranslation->resume();

        return $this;
    }

    /**
     * Validate Form data
     *
     * @return bool|string[]
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function validate()
    {
        $errors = [];

        $name = $this->getSender()->getName();
        if (empty($name)) {
            $errors[] = __('Please enter a sender name.');
        }

        $email = $this->getSender()->getEmail();
        if (empty($email) or !\Zend_Validate::is($email, 'EmailAddress')) {
            $errors[] = __('Invalid Sender Email.');
        }

        $message = $this->getSender()->getMessage();
        if (empty($message)) {
            $errors[] = __('Please enter a message.');
        }

        if (!$this->getRecipients()->getEmails()) {
            $errors[] = __('Please specify at least one recipient.');
        }

        // validate recipients email addresses
        foreach ($this->getRecipients()->getEmails() as $email) {
            if (!\Zend_Validate::is($email, 'EmailAddress')) {
                $errors[] = __('Please enter a correct recipient email address.');
                break;
            }
        }

        if (count($this->getRecipients()->getEmails()) > self::MAX_RECIPIENTS) {
            $errors[] = __('No more than %1 emails can be sent at a time.', self::MAX_RECIPIENTS);
        }

        if (empty($errors)) {
            return true;
        }

        return $errors;
    }

    /**
     * Set Recipient
     *
     * @param string $recipient
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function setRecipient($recipient)
    {
        $emails = [$recipient];
        $names = [$recipient];

        return $this->setData(
            '_recipients',
            new \Magento\Framework\DataObject(['emails' => $emails, 'names' => $names])
        );
    }

    /**
     * Retrieve Recipients object
     *
     * @return \Magento\Framework\DataObject
     */
    public function getRecipients()
    {
        $recipients = $this->_getData('_recipients');
        if (!$recipients instanceof \Magento\Framework\DataObject) {
            $recipients = new \Magento\Framework\DataObject(['emails' => [], 'names' => []]);
            $this->setData('_recipients', $recipients);
        }
        return $recipients;
    }

    /**
     * Set Sender Information array
     *
     * @param array $sender
     * @return $this
     */
    public function setSender($sender)
    {
        if (!is_array($sender)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Invalid Sender Information.')
            );
        }

        return $this->setData('_sender', new \Magento\Framework\DataObject($sender));
    }

    /**
     * Retrieve Sender Information Object
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Magento\Framework\DataObject
     */
    public function getSender()
    {
        $sender = $this->_getData('_sender');
        if (!$sender instanceof \Magento\Framework\DataObject) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Please define the correct sender information.')
            );
        }
        return $sender;
    }
}

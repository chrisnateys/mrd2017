<?php
/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIds\SocialShare\Controller\Adminhtml\Service;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use SalesIds\SocialShare\Model\ServiceRepository;

class Delete extends Action
{
    /**
     * @var ServiceRepository
     */
    protected $_serviceRepository;

    /**
     * @param Context $context
     * @param ServiceRepository $serviceRepository
     */
    public function __construct(
        Context $context,
        ServiceRepository $serviceRepository
    ) {
        $this->_serviceRepository = $serviceRepository;
        parent::__construct($context);
    }

    /**
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                /** @var \SalesIds\SocialShare\Model\Service $model */
                $model = $this->_serviceRepository->getById($id);
                $this->_serviceRepository->delete($model);
                $this->messageManager->addSuccessMessage(__('You deleted the service.'));
                $this->_redirect('salesids_socialshare/*/');
                return;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __("We can't delete this service right now. Please review the log and try again.")
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_redirect('salesids_socialshare/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->messageManager->addErrorMessage(__("We can't find a service to delete."));
        $this->_redirect('salesids_socialshare/*/');
    }
}

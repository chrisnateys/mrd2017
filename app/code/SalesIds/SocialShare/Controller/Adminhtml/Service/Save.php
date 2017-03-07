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

class Save extends Action
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
     * Execute
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        try {
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();

            /** @var \SalesIds\SocialShare\Model\Service $model */
            $model = $this->_objectManager->create('SalesIds\SocialShare\Model\Service');
            $this->_eventManager->dispatch(
                'adminhtml_controller_salesids_socialshare_service_prepare_save',
                ['request' => $this->getRequest()]
            );
            $data = $this->getRequest()->getPostValue();
            $id = $this->getRequest()->getParam('id');

            if ($id) {
                $model = $this->_serviceRepository->getById($id);
                if ($id != $model->getId()) {
                    throw new LocalizedException(__('Wrong service specified.'));
                }
                $model->addData($data);
            } else {
                $model->setData($data);
            }

            // save the data
            $this->_serviceRepository->save($model);

            // display success message
            $this->messageManager->addSuccessMessage(__('You saved the service.'));

            // clear previously saved data from session
            $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);

            // check if 'Save and Continue'
            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
            }

            // go to grid
            return $resultRedirect->setPath('*/*/');
        } catch (\Exception $e) {
            // display error message
            $this->messageManager->addErrorMessage($e->getMessage());
            // save data in session
            $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData($data);
            // redirect to edit form
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        $this->_redirect('salesids_socialshare/*/');
    }
}

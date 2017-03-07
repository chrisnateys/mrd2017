<?php
/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIds\SocialShare\Controller\Adminhtml\Service;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('SalesIds_SocialShare::service')
            ->addBreadcrumb(__('Social Services'), __('Social Services'));

        return $resultPage;
    }

    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param Date $dateFilter
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('SalesIds\SocialShare\Model\Service');

        if ($id) {
            $model->load($id);
            if (!$model->getServiceId()) {
                $this->messageManager->addError(__('This service no longer exists.'));
                $this->_redirect('salesids_socialshare/*');
                return;
            }
        }

        // set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->_coreRegistry->register('current_service', $model);

        // 5. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Service') : __('New Service'),
            $id ? __('Edit Service') : __('New Service')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Services'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getName() : __('New Service'));

        return $resultPage;
    }
}

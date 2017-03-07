<?php
/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIds\SocialShare\Block\Adminhtml\Service\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('salesids_socialshare_service');
        $this->setTitle(__('Service Information'));
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_service');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl('salesids_socialshare/service/save'),
                    'method' => 'post',
                ],
            ]
        );

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General Information'), 'class' => 'fieldset-wide']
        );

        if ($model->getServiceId()) {
            $fieldset->addField('service_id', 'hidden', ['name' => 'id']);
        }

        $fieldset->addField('code', 'text', [
            'name' => 'code',
            'label' => __('Code'),
            'title' => __('Code'),
            'required' => true,
            'class' => 'validate-xml-code'
        ]);

        $fieldset->addField('name', 'text', [
            'name' => 'name',
            'label' => __('Name'),
            'title' => __('Name'),
            'required' => true,
            'class' => 'validate-xml-name'
        ]);

        $fieldset->addField('url', 'text', [
            'name' => 'url',
            'label' => __('Url'),
            'title' => __('Url'),
            'required' => false,
            'class' => 'validate-xml-url',
            'note' => __('Note that if this field is empty, a template must be set.')
        ]);

        $fieldset->addField('color', 'text', [
            'name' => 'color',
            'label' => __('Color'),
            'title' => __('Color'),
            'required' => true,
            'class' => 'validate-xml-color'
        ]);

        $fieldset->addField('icon', 'text', [
            'name' => 'icon',
            'label' => __('Icon'),
            'title' => __('Icon'),
            'required' => true,
            'class' => 'validate-xml-icon',
            'note' => __('Both <a href="http://www.socicon.com/chart.php" target="_blank">socicon</a> and <a href="http://fontawesome.io/icons/" target="_blank">font awesome</a> classes are available here.')
        ]);

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}

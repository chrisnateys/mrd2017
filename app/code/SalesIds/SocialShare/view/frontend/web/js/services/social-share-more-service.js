/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

/*jshint browser:true jquery:true*/
/*global modal:true*/
define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/translate'
], function($, modal){
    "use strict";
    
    /**
     * ProductListCatalogAjaxFilter Widget
     *
     * This widget is submitting form in AJAX when a change is done from the
     * layered navigation block
     */
    $.widget('mage.socialShareMoreService', {

        /**
         * Widget options
         */
        options: {
            modalContainer: '.more-modal-container', // Contains modal content
            modalOptions: {
                autoOpen: true,
                type: 'popup',
                modalClass: 'salesids-socialshare-more-modal',
                responsive: true,
                innerScroll: true,
                title: $.mage.__('Share'),
                buttons: [] // No buttons
            }       
        },

        /**
         * Bind elements when the class is instantiated
         *
         * @return void
         */
        _create: function () {
            this._bind($(this.element));
        },

        /**
         * Bind an element
         *
         * @param object element
         * @return void
         */
        _bind: function (element) {
            if (element.is('a')) {
                element.on('click', $.proxy(this._openModal, this));
            }
        },

        /**
         * Open modal box
         * 
         * @return void
         */
        _openModal: function() {
            var modalBox = modal(this.options.modalOptions, $(this.options.modalContainer));
            return false;
        }
    });

    return $.mage.socialShareMoreService;
});
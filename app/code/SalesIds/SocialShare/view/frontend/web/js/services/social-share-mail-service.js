/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/translate'
], function($, modal) {
    "use strict";

    //flag check if mail is already send
    var mailSend = false;

    $.widget('mage.socialShareMailService', {

        /**
         * Options
         *
         * @var object
         */
        options: {
            modalContainer: '.salesids-socialshare-mail-modal',
            ajaxSpinnerSelector: '#modal-loader',
            messagesContainer: '.message'
        },

        /**
         * Create element
         */
        _create: function() {
            this.element.on('click', $.proxy(this._showModal, this));
        },

        /**
         * Display modal box
         *
         * @param Event event
         */
        _showModal: function(event) {

            var self = this;

            $(this.options.before).show();
            $(this.options.after).hide();

            var options = {
                type: 'popup',
                modalClass: 'salesids-socialshare-mail-modal',
                responsive: true,
                innerScroll: false,
                modalLeftMargin: 0,
                autoOpen: true,
                title: $.mage.__('Share via e-mail'),
                buttons: [{
                    text: $.mage.__('Send'),
                    class: 'action-primary submit',
                    click: function () {
                        self._ajaxPost();
                    }
                }]
            };

            var modalBox = modal(options, $(this.options.modalContainer));

            if (this.options.captchaKey && this.options.captchaElement) {
                grecaptcha.render(this.options.captchaElement, {
                  'sitekey' : this.options.captchaKey,
                })
            }

            event.preventDefault();
        },

        /**
         * Post of the form
         *
         * @return bool
         */
        _ajaxPost: function() {
            var form = this._getForm();
            // before submitting the form we need to run the javascript validation of fieldss
            if (form.mage('validation').valid()) {
                this._setSpinner(true);
                $.post(form.attr('action'), form.serialize(), $.proxy(function(response) {
                    if (response.success) {
                        if (this.options.captchaKey && this.options.captchaElement) {
                            // Reset captcha on success call
                            grecaptcha.reset();
                        }
                    }
                    // Display messages
                    response.data.messages.forEach($.proxy(function(mesage) {
                        this._setMessage(mesage, response.success);
                    }, this));
                    this._setSpinner(false);
                }, this));
            }

            return false;
        },

        /**
         * Get modal form
         *
         * @return DOM_Element
         */
        _getForm: function() {
            return $(this.options.modalContainer).find('form');
        },

        /**
         * Get messages container
         *
         * @return DOM_Element
         */
        _getMessagesContainer: function() {
            return $(this.options.modalContainer).find(this.options.messagesContainer);
        },

        /**
         * Set main popup response message
         *
         * @var string message
         * @var bool success
         * @return void
         */
        _setMessage: function(message, success) {
            var messagesContainer = this._getMessagesContainer();

            // Set state
            var type = success ? 'success' : 'error';
            messagesContainer.removeClass('success').removeClass('error');

            // Build message
            var messageContent = $('<div />').append(message);
            messagesContainer.addClass(type).html(messageContent);

            return;
        },

        /**
         * Set spinner state
         *
         * @var bool state
         * @return void
         */
        _setSpinner: function(state) {
            if (state) {
                this.element.attr('disabled', 'disabled');
                return $(this.options.modalContainer).find(this.options.ajaxSpinnerSelector).show();
            }
            this.element.removeAttr('disabled');
            return $(this.options.modalContainer).find(this.options.ajaxSpinnerSelector).hide();
        }
    });

    return $.mage.socialShareMailService;
});
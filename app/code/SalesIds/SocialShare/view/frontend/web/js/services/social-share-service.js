/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery'
], function($) {
    "use strict";

    //flag check if mail is already send
    var mailSend = false;

    $.widget('mage.socialShareService', {

        /**
         * Create element
         */
        _create: function() {
            this.element.on('click', $.proxy(this._emulateChildClick, this));
        },

        /**
         * Emulate first child link click
         *
         * @param Event event
         */
        _emulateChildClick: function(event) {
            $(event.target).find('a').first().trigger('click');
        }
    });

    return $.mage.socialShareService;
});
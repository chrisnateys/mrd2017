/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/* eslint-disable no-undef */
// jscs:disable jsDoc

define([
    'jquery',
    'mage/template',
    'jquery/ui',
    'prototype',
    'form',
    'validation'
], function (jQuery, mageTemplate) {
    'use strict';

    return function (config) {
        var services = {
            table: config.table,
            template: mageTemplate(config.rowTemplate),
            render: function () {
                var elements = '';
                config.services.each(jQuery.proxy(function(element) {
                    elements += this.template({
                        data: element
                    });
                }, this));
                Element.insert($$('[data-role=' + (config.container) + ']')[0], elements);
            }
        };

        if (config.isSortable) {
            jQuery(function ($) {
                $('[data-role=' + (config.container) + ']').sortable({
                    distance: 8,
                    tolerance: 'pointer',
                    cancel: 'input, button',
                    axis: 'y',
                    update: function () {
                            $('[data-role=' + (config.container) + '] [data-role=order]').each(function (index, element) {
                            $(element).val(index + 1);
                        });
                    }
                });
            });
            services.render();
        }
    };
});

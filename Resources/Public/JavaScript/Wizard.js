/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Module: TYPO3/CMS/Grid/Wizard
 * this JS code does the form logic for the content element wizard
 * based on jQuery UI
 */
define(['jquery', 'TYPO3/CMS/Backend/Modal', 'TYPO3/CMS/Backend/Severity'], function($, Modal, Severity) {
    'use strict';

    var Wizard = {
        identifier: {
            container: '.t3js-content-wizard-container',
            item: '[data-parameters]'
        }
    };

    Wizard.initialize = function() {
        $(this).find(Wizard.identifier.container).each(function() {
            let url = $(this).attr('data-url');
            let parameters = JSON.parse($(this).attr('data-parameters'));

            $(this).find(Wizard.identifier.item).on('click', function(event) {
                event.preventDefault();

                parameters = $.extend(true, parameters, JSON.parse($(this).attr('data-parameters')));

                if (!$(this).is('[data-slide=next]')) {
                    window.location.href = url + (url.indexOf('?') > -1 ? '&' : '?') + $.param(parameters);
                }
            });
        });
    };
    
    Wizard.show = function(url, title) {
        Modal.advanced({
            callback: function(currentModal) {
                currentModal.find('.t3js-modal-body').addClass('t3-content-wizard-window');
                currentModal.on('modal-loaded', function() {
                    $.proxy(Wizard.initialize, this)();
                });
            },
            content: url,
            severity: Severity.notice,
            size: Modal.sizes.large,
            title: title,
            type: Modal.types.ajax,
        });
    };

    $(Wizard.initialize);

    return Wizard;
});

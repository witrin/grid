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
define(["require", "exports", "jquery", "TYPO3/CMS/Backend/Modal", "TYPO3/CMS/Backend/Severity"], function (require, exports, $, Modal, Severity) {
    "use strict";
    /**
     * Wizard for grid content creation
     *
     * @exports TYPO3/CMS/Grid/Wizard/Creation
     */
    var Creation = (function () {
        function Creation() {
        }
        /**
         * Show content creation wizard via modal
         *
         * @param url
         * @param title
         */
        Creation.prototype.show = function (url, title) {
            var _this = this;
            Modal.advanced({
                callback: function (modal) {
                    $(modal).find('.t3js-modal-body').addClass('t3-grid-content-creation-wizard-window');
                    $(modal).on('modal-loaded', function (event) {
                        _this.modal = modal;
                        _this.initialize();
                    });
                },
                content: "" + url,
                severity: Severity.notice,
                size: Modal.sizes.large,
                title: "" + title,
                type: Modal.types.ajax,
            });
        };
        /**
         * Initialize logic for content creation wizard
         */
        Creation.prototype.initialize = function () {
            var $container = this.modal ?
                $(this.modal).find(Creation.identifier.container) :
                $(Creation.identifier.container);
            $container.each(function (index, element) {
                var url = $(element).attr('data-url');
                var parameters = JSON.parse($(element).attr('data-parameters'));
                $(element).find(Creation.identifier.item).on('click', function (event) {
                    event.preventDefault();
                    parameters = $.extend(true, parameters, JSON.parse($container.attr('data-parameters')));
                    if (!$(event.currentTarget).is('[data-slide=next]')) {
                        var query = {
                            defVals: (_a = {}, _a[parameters.table] = parameters.defaults, _a),
                            overrideVals: (_b = {}, _b[parameters.table] = parameters.overrides, _b),
                        };
                        window.location.href = url + (url.indexOf('?') > -1 ? '&' : '?') + $.param(query);
                    }
                    var _a, _b;
                });
            });
        };
        Creation.identifier = {
            container: '.t3js-grid-content-creation-wizard-container',
            item: '.t3js-grid-content-creation-wizard-item',
        };
        return Creation;
    }());
    return new Creation();
});

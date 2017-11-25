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
define(["require", "exports", "jquery", "TYPO3/CMS/Backend/AjaxDataHandler", "TYPO3/CMS/Backend/Modal", "TYPO3/CMS/Backend/Severity"], function (require, exports, $, AjaxDataHandler, Modal, Severity) {
    "use strict";
    /**
     *
     * @export TYPO3/CMS/Grid/Paste
     */
    var Paste = (function () {
        function Paste() {
        }
        /**
         * Initializes handler for paste actions in the container
         */
        Paste.prototype.initialize = function () {
            var _this = this;
            $('body').find(Paste.identifier.item).on('click', function (event) {
                event.preventDefault();
                _this.onStart(event.currentTarget);
            });
        };
        /**
         * Called when paste finishes
         *
         * @param item
         * @todo Error handling
         */
        Paste.prototype.onFinish = function (item) {
            var uid = parseInt($(item).data('uid'), 10);
            var target = parseInt($(item).data('target'), 10);
            var parameters = $(item).data('parameters');
            var data = $.extend(true, {}, parameters.defaults, parameters.overrides);
            var query;
            // check requested action
            if ($(item).is(Paste.identifier.mode.copy)) {
                query = {
                    cmd: (_a = {}, _a[parameters.table] = (_b = {}, _b[uid] = { copy: { action: 'paste', target: target, update: data } }, _b), _a),
                };
            }
            else {
                query = {
                    cmd: (_c = {}, _c[parameters.table] = (_d = {}, _d[uid] = { move: target }, _d), _c),
                    data: (_e = {}, _e[parameters.table] = (_f = {}, _f[uid] = data, _f), _e),
                };
            }
            Modal.currentModal.trigger('modal-dismiss');
            AjaxDataHandler.process(query).then(function (result) {
                self.location.reload(true);
            });
            var _a, _b, _c, _d, _e, _f;
        };
        /**
         * Called when paste starts
         *
         * @param element
         */
        Paste.prototype.onStart = function (element) {
            var _this = this;
            var title = (TYPO3.lang['paste.modal.title.paste'] || 'Paste record') + ": \"" + $(element).data('title') + "\"";
            var severity = top.TYPO3.Severity[$(element).data('severity')] || top.TYPO3.Severity.info;
            var buttons = [
                {
                    active: true,
                    btnClass: 'btn-default',
                    text: TYPO3.lang['paste.modal.button.cancel'] || 'Cancel',
                    trigger: function () { return Modal.currentModal.trigger('modal-dismiss'); },
                },
            ];
            var content;
            if ($(element).is(Paste.identifier.mode.copy)) {
                content = TYPO3.lang['paste.modal.pastecopy'] || 'Do you want to copy the record to this position?';
                buttons.push({
                    btnClass: 'btn-' + Severity.getCssClass(severity),
                    text: TYPO3.lang['paste.modal.button.pastecopy'] || 'Copy',
                    trigger: function () { return _this.onFinish(element); },
                });
            }
            else {
                content = TYPO3.lang['paste.modal.paste'] || 'Do you want to move the record to this position?';
                buttons.push({
                    btnClass: 'btn-' + Severity.getCssClass(severity),
                    text: TYPO3.lang['paste.modal.button.paste'] || 'Move',
                    trigger: function () { return _this.onFinish(element); },
                });
            }
            Modal.show(title, content, severity, buttons);
        };
        Paste.identifier = {
            item: '.t3js-grid-paste',
            mode: {
                copy: '.t3js-grid-paste-copy',
                move: '.t3js-grid-paste-move',
            },
        };
        return Paste;
    }());
    return new Paste();
});

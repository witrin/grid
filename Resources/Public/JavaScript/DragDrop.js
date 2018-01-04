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
define(["require", "exports", "jquery", "TYPO3/CMS/Backend/AjaxDataHandler", "jquery-ui/draggable", "jquery-ui/droppable"], function (require, exports, $, AjaxDataHandler) {
    "use strict";
    /**
     * Drag type
     */
    var DragType;
    (function (DragType) {
        DragType[DragType["Record"] = 0] = "Record";
        DragType[DragType["Preset"] = 1] = "Preset";
    })(DragType || (DragType = {}));
    /**
     * Handle drag and drop of records and presets within a grid container
     *
     * @exports TYPO3/CMS/Grid/DragDrop
     * @todo Support multiple grid container
     */
    var DragDrop = (function () {
        function DragDrop() {
        }
        /**
         * Initialize drag and drop for all records and presets on the grid container
         */
        DragDrop.prototype.initialize = function () {
            var _this = this;
            var table = 'pages';
            // wire draggables
            $(DragDrop.identifier.item).each(function (index, element) {
                $(element).draggable({
                    addClasses: true,
                    appendTo: 'body',
                    cursor: 'move',
                    distance: 20,
                    handle: DragDrop.identifier.handle,
                    helper: 'clone',
                    revert: 'invalid',
                    scope: table,
                    start: function (event, ui) { return _this.onDragStart(element, ui.helper); },
                    stop: function (event, ui) { return _this.onDragStop(element, ui.helper); },
                    zIndex: 100,
                });
            });
            // wire droppables
            $(DragDrop.identifier.zone).each(function (index, element) {
                $(element).droppable({
                    accept: function ($draggable) { return _this.isDropAllowed(element, $draggable.get(0)); },
                    activeClass: DragDrop.styles.zone.active,
                    drop: function (event, ui) { return _this.onDrop(ui.draggable, element, event); },
                    hoverClass: DragDrop.styles.zone.hover,
                    scope: table,
                    tolerance: 'pointer',
                });
            });
        };
        /**
         * Check if draggable can be dropped on given droppable
         *
         * @param droppable
         * @param draggable
         */
        DragDrop.prototype.isDropAllowed = function (zone, item) {
            var target = Math.abs(Number($(zone).data('target')));
            var $area = $(zone).closest(DragDrop.identifier.area);
            var $content = $area.find(DragDrop.identifier.item + "[data-uid=" + target + "]");
            var $siblings = $area.find(DragDrop.identifier.item);
            // only allowed if item is not a record or zone is not a direct sibling of the item
            return this.getDragType(item) === DragType.Preset ||
                $siblings.index($(item)) < 0 ||
                $siblings.index($(item)) - 1 !== $siblings.index($content) &&
                    $siblings.index($(item)) !== $siblings.index($content);
        };
        /**
         * Called when draggable is selected to be moved
         *
         * @param item Draggable element
         * @param clone Draggable element clone
         */
        DragDrop.prototype.onDragStart = function (item, clone) {
            // update container status
            $(DragDrop.identifier.container).addClass(DragDrop.styles.container.active);
            // prepare common item
            $(clone).css('width', $(item).outerWidth());
            // prepare record item
            if (this.getDragType(item) === DragType.Record) {
                $(item).css('visibility', 'hidden');
                $(clone).append("<div class=\"ui-draggable-copy-message\">" + TYPO3.lang['dragdrop.copy.message'] + "</div>");
            }
        };
        /**
         * Called when a draggable is released
         *
         * @param item Draggable element
         * @param clone Draggable element clone
         */
        DragDrop.prototype.onDragStop = function (item, clone) {
            // update container status
            $(DragDrop.identifier.container).removeClass(DragDrop.styles.container.active);
            // prepare if item is a record
            if (this.getDragType(item) === DragType.Record) {
                $(item).attr('style', null);
            }
        };
        /**
         * Called when a draggable is dropped
         *
         * @param item Draggable element
         * @param zone Droppable element
         * @param event Drop event
         * @todo Handle exceptions
         */
        DragDrop.prototype.onDrop = function (item, zone, event) {
            var _this = this;
            var uid = parseInt($(item).data('uid'), 10);
            var target = parseInt($(zone).data('target'), 10);
            var parameters = $(zone).data('parameters');
            var data = $.extend(true, {}, parameters.defaults, parameters.overrides);
            var query;
            if (this.getDragType(item) === DragType.Record) {
                // check requested action
                if (event && event.originalEvent.ctrlKey) {
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
            }
            else {
                // @todo What about `rootLevel !== 0`?
                var values = $(item).data('values');
                data = $.extend(true, {}, data, values.defaults, values.overrides);
                query = { data: (_g = {}, _g[parameters.table] = { NEW: data }, _g) };
            }
            AjaxDataHandler.process(query).then(function (result) {
                if (_this.getDragType(item) === DragType.Record) {
                    $(item).parent().detach().insertAfter($(zone).parentsUntil(DragDrop.identifier.area).addBack().get(0));
                }
                else {
                    $(item).clone().wrap('<div class="t3-grid-wrapper"></div>').insertAfter($(zone).parentsUntil(DragDrop.identifier.area).addBack().get(0));
                }
                self.location.reload(true);
            }).catch(function (error) {
                console.log(error);
            });
            var _a, _b, _c, _d, _e, _f, _g;
        };
        /**
         * Get type of draggable
         *
         * @param item Draggable element
         */
        DragDrop.prototype.getDragType = function (item) {
            return $(item).is('[data-uid]') ? DragType.Record : DragType.Preset;
        };
        DragDrop.identifier = {
            area: '.t3js-grid-drag-drop-area',
            container: '.t3js-grid-drag-drop-container',
            handle: '.t3js-grid-drag-drop-handle',
            item: '.t3js-grid-drag-drop-item',
            zone: '.t3js-grid-drag-drop-zone',
        };
        DragDrop.styles = {
            container: {
                active: 't3-grid-drag-drop-active',
            },
            zone: {
                active: 't3-grid-drag-drop-zone-active',
                hover: 't3-grid-drag-drop-zone-hover',
            },
        };
        return DragDrop;
    }());
    return new DragDrop();
});

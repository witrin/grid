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
define(["require", "exports", "jquery", "jquery-ui/resizable"], function (require, exports, $) {
    "use strict";
    /**
     * Toggle states
     */
    var ToggleState;
    (function (ToggleState) {
        ToggleState["Collapsed"] = "collapsed";
        ToggleState["Expanded"] = "expanded";
        ToggleState["FullExpanded"] = "full-expanded";
    })(ToggleState || (ToggleState = {}));
    /**
     * Handle toggling of a backend module sidebar
     *
     * @exports TYPO3/CMS/Grid/Sidebar
     * @todo Move to sysext `backend`
     */
    var Sidebar = (function () {
        function Sidebar() {
        }
        /**
         * Get toggle state
         *
         * @param start
         * @param offset
         */
        Sidebar.getState = function (start, offset) {
            var values = Object.keys(ToggleState).map(function (k) { return ToggleState[k]; });
            var n = values.length;
            return values[(($.inArray(start, values) + offset) % n + n) % n];
        };
        /**
         * Initialize sidebar
         */
        Sidebar.prototype.initialize = function () {
            var _this = this;
            var $element = $(Sidebar.identifier.element);
            var $toggle = $(Sidebar.identifier.toggle);
            $toggle.on('click', function (event) { return _this.onToggle(); });
            $(window).on('resize', function (event) { return _this.onUpdate(); });
            $(Sidebar.identifier.split).css({ right: this.calculateOffset() });
            $toggle.css({ visibility: 'visible' });
            if ($element.attr('data-resizable') !== undefined) {
                $element.resizable({
                    handles: {
                        w: Sidebar.identifier.split,
                    },
                    resize: function (event, ui) { return _this.onResize(); },
                    start: function (event, ui) { return _this.onStart(); },
                });
                $.each(['minWidth', 'maxWidth'], function (index, option) {
                    if ($element.data().hasOwnProperty(option)) {
                        $element.resizable('option', option, $element.data(option));
                    }
                });
            }
            this.onUpdate();
        };
        /**
         * Called when sidebar needs update
         */
        Sidebar.prototype.onUpdate = function () {
            $(Sidebar.identifier.panel).css('height', $(window).height() + 'px');
        };
        /**
         * Called when sidebar toggles
         */
        Sidebar.prototype.onToggle = function () {
            var $element = $(Sidebar.identifier.element);
            var state = $element.attr('data-toggle');
            $element.attr('data-toggle', Sidebar.getState(state, 1));
            $element.removeAttr('style');
            if (Sidebar.getState(state, 2) === ToggleState.Collapsed) {
                $element.removeAttr('data-expandable');
            }
            else {
                $element.attr('data-expandable', '');
                if ($element.attr('data-size')) {
                    $element.css('width', $element.attr('data-size'));
                }
            }
            $(Sidebar.identifier.module).css('width', 'calc( 100% - ' + this.calculateWidth() + 'px )');
        };
        /**
         * Called on sidebar resize
         */
        Sidebar.prototype.onResize = function () {
            var $element = $(Sidebar.identifier.element);
            $element.removeAttr('data-collapsed');
            $element.attr('data-toggle', Sidebar.getState(ToggleState.Collapsed, -1));
        };
        /**
         * Called on sidebar start
         */
        Sidebar.prototype.onStart = function () {
            var width = this.calculateWidth();
            // See https://bugs.jqueryui.com/ticket/4985
            $(this).css('left', '');
            $(this).attr('data-size', width);
            $(Sidebar.identifier.module).css('width', 'calc( 100% - ' + width + 'px )');
            $(Sidebar.identifier.split).css('right', this.calculateOffset());
        };
        /**
         * Calculate sidebar width
         *
         * @returns number
         */
        Sidebar.prototype.calculateWidth = function () {
            return $(Sidebar.identifier.element).outerWidth();
        };
        /**
         * Calculate sidebar offset
         *
         * @returns number
         */
        Sidebar.prototype.calculateOffset = function () {
            var $module = $(Sidebar.identifier.module);
            return $module.get(0).offsetWidth - $module.get(0).clientWidth + $(Sidebar.identifier.split).outerWidth();
        };
        /**
         * Identifier for sidebar elements
         */
        Sidebar.identifier = {
            element: '.t3js-sidebar',
            module: '.module, .module-docheader',
            panel: '.t3js-sidebar-panel',
            split: '.t3js-sidebar-split',
            toggle: '.t3js-sidebar-toggle',
        };
        return Sidebar;
    }());
    return new Sidebar();
});

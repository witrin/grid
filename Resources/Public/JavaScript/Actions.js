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
define(["require", "exports", "jquery", "TYPO3/CMS/Backend/Storage/Persistent", "TYPO3/CMS/Grid/Wizard/Creation"], function (require, exports, $, PersistentStorage, ContentCreationWizard) {
    "use strict";
    /**
     * Handel actions within grid container
     *
     * @exports TYPO3/CMS/Grid/Actions
     * @todo Make this more generic.
     */
    var Actions = (function () {
        function Actions() {
        }
        /**
         * Intialize actions for grid container
         */
        Actions.prototype.initialize = function () {
            var _this = this;
            this.$container = $(Actions.identifier.container);
            this.$toggleHiddenContentVisibilityCheckbox = $(Actions.identifier.hiddenContentVisibilityToggle);
            this.$showContentWizardButton = $(Actions.identifier.showContentCreationWizard);
            this.$toggleHiddenContentVisibilityCheckbox.on('change', function (event) { return _this.onToggleHiddenContentVisibilityCheckboxChanged(event); });
            this.$showContentWizardButton.on('click', function (event) { return _this.onShowContentWizardClicked(event); });
        };
        /**
         * Called when checkbox for toggling visibility of hidden content has changed
         *
         * @param event
         */
        Actions.prototype.onToggleHiddenContentVisibilityCheckboxChanged = function (event) {
            var table = this.$container.data('table');
            var field = this.$container.data('field');
            var $hiddenContent = $(Actions.identifier.hiddenContent);
            var $spinner = $('<span />', { class: 'checkbox-spinner fa fa-circle-o-notch fa-spin' });
            $(event.currentTarget).hide().after($spinner);
            if ($(event.currentTarget).prop('checked')) {
                $hiddenContent.slideDown();
            }
            else {
                $hiddenContent.slideUp();
            }
            PersistentStorage.set("tx_grid." + table + "." + field + ".showHiddenContent", $(event.currentTarget).prop('checked') ? '1' : '0').done(function () {
                $spinner.remove();
                $(event.currentTarget).show();
            });
        };
        /**
         * Called when button for showing content creation wizard is clicked
         *
         * @param event
         */
        Actions.prototype.onShowContentWizardClicked = function (event) {
            ContentCreationWizard.show($(event.currentTarget).data('url'), $(event.currentTarget).data('title'));
        };
        Actions.identifier = {
            container: '.t3js-grid-actions-container',
            hiddenContent: '.t3js-hidden-content',
            hiddenContentVisibilityToggle: '.t3js-hidden-content-visibility-toggle',
            showContentCreationWizard: '.t3js-content-wizard-show',
        };
        return Actions;
    }());
    return new Actions();
});

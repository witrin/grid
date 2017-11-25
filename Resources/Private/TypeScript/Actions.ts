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

import $ = require('jquery');
import PersistentStorage = require('TYPO3/CMS/Backend/Storage/Persistent');
import ContentCreationWizard = require('TYPO3/CMS/Grid/Wizard/Creation');

/**
 * Handel actions within grid container
 *
 * @exports TYPO3/CMS/Grid/Actions
 * @todo Make this more generic.
 */
class Actions {

  protected static readonly identifier = {
    container: '.t3js-grid-actions-container',
    hiddenContent: '.t3js-hidden-content',
    hiddenContentVisibilityToggle: '.t3js-hidden-content-visibility-toggle',
    showContentCreationWizard: '.t3js-content-wizard-show',
  };

  protected $toggleHiddenContentVisibilityCheckbox: JQuery;

  protected $showContentWizardButton: JQuery;

  protected $container: JQuery;

  /**
   * Intialize actions for grid container
   */
  public initialize() {
    this.$container = $(Actions.identifier.container);
    this.$toggleHiddenContentVisibilityCheckbox = $(Actions.identifier.hiddenContentVisibilityToggle);
    this.$showContentWizardButton = $(Actions.identifier.showContentCreationWizard);

    this.$toggleHiddenContentVisibilityCheckbox.on(
      'change',
      (event: any) => this.onToggleHiddenContentVisibilityCheckboxChanged(event),
    );

    this.$showContentWizardButton.on(
      'click',
      (event: any) => this.onShowContentWizardClicked(event),
    );
  }

  /**
   * Called when checkbox for toggling visibility of hidden content has changed
   *
   * @param event
   */
  protected onToggleHiddenContentVisibilityCheckboxChanged(event: any): void {
    const table = this.$container.attr('data-table');
    const field = this.$container.attr('data-field');
    const $hiddenContent = $(Actions.identifier.hiddenContent);
    const $spinner = $('<span />', { class: 'checkbox-spinner fa fa-circle-o-notch fa-spin' });

    $(event.currentTarget).hide().after($spinner);

    if ($(event.currentTarget).prop('checked')) {
      $hiddenContent.slideDown();
    } else {
      $hiddenContent.slideUp();
    }

    PersistentStorage.set(
      `tx_grid.${table}.${field}.showHiddenContent`,
      $(event.currentTarget).prop('checked') ? '1' : '0',
    ).done(() => {
      $spinner.remove();
      $(event.currentTarget).show();
    });
  }

  /**
   * Called when button for showing content creation wizard is clicked
   *
   * @param event
   */
  protected onShowContentWizardClicked(event: any) {
    ContentCreationWizard.show(
      $(event.currentTarget).attr('data-url'),
      $(event.currentTarget).attr('data-title'),
    );
  }
}

export = new Actions();

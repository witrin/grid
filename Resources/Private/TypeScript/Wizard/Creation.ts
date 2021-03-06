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
import Modal = require('TYPO3/CMS/Backend/Modal');
import Severity = require('TYPO3/CMS/Backend/Severity');

/**
 * Wizard for grid content creation
 *
 * @exports TYPO3/CMS/Grid/Wizard/Creation
 */
class Creation {

  protected static readonly identifier: { [key: string]: string } = {
    container: '.t3js-grid-content-creation-wizard-container',
    item: '.t3js-grid-content-creation-wizard-item',
  };

  protected modal: Element;

  /**
   * Show content creation wizard via modal
   *
   * @param url
   * @param title
   */
  public show(url: string, title: string): void {
    Modal.advanced({
      ajaxCallback: () => {
        this.modal = Modal.currentModal.get(0);

        $(this.modal).find('.t3js-modal-body').addClass('t3-grid-content-creation-wizard-window');

        this.initialize();
      },
      content: `${url}`,
      severity: Severity.notice,
      size: Modal.sizes.large,
      title: `${title}`,
      type: Modal.types.ajax,
    });
  }

  /**
   * Initialize logic for content creation wizard
   */
  public initialize(): void {
    const $container: JQuery = this.modal ?
      $(this.modal).find(Creation.identifier.container) :
      $(Creation.identifier.container);

    $container.each((index: number, element: Element) => {
      const url = $(element).data('url');
      let parameters = $(element).data('parameters');

      $(element).find(Creation.identifier.item).on('click', (event: any) => {
        event.preventDefault();

        parameters = $.extend(true, parameters, $(event.currentTarget).data('parameters'));

        if (!$(event.currentTarget).is('[data-slide=next]')) {
          const query = {
            edit: parameters.edit,
            defVals: { [parameters.table]: parameters.defaults },
            overrideVals: { [parameters.table]: parameters.overrides },
          };

          window.location.href = url + (url.indexOf('?') > -1 ? '&' : '?') + $.param(query);
        }
      });
    });
  }
}

export = new Creation();

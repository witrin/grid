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
import AjaxDataHandler = require('TYPO3/CMS/Backend/AjaxDataHandler');
import Modal = require('TYPO3/CMS/Backend/Modal');
import Severity = require('TYPO3/CMS/Backend/Severity');

/**
 *
 * @export TYPO3/CMS/Grid/Paste
 */
class Paste {

  protected static readonly identifier = {
    item: '.t3js-grid-paste',
    mode: {
      copy: '.t3js-grid-paste-copy',
      move: '.t3js-grid-paste-move',
    },
  };

  /**
   * Initializes handler for paste actions in the container
   */
  public initialize(): void {
    $('body').find(Paste.identifier.item).on('click', (event: any) => {
      event.preventDefault();
      this.onStart(event.currentTarget);
    });
  }

  /**
   * Called when paste finishes
   *
   * @param item
   * @todo Error handling
   */
  protected onFinish(item: Element): void {
    const uid = parseInt($(item).attr('data-uid'), 10);
    const target = parseInt($(item).attr('data-target'), 10);
    const parameters = JSON.parse($(item).attr('data-parameters'));
    const data = $.extend(true, {}, parameters.defaults, parameters.overrides);

    let query: object;

    // check requested action
    if ($(item).is(Paste.identifier.mode.copy)) {
      query = {
        cmd: { [parameters.table]: { [uid]: { copy: { action: 'paste', target, update: data } } } },
      };
    } else {
      query = {
        cmd: { [parameters.table]: { [uid]: { move: target } } },
        data: { [parameters.table]: { [uid]: data } },
      };
    }

    Modal.currentModal.trigger('modal-dismiss');

    AjaxDataHandler.process(query).then((result) => {
      self.location.reload(true);
    });
  }

  /**
   * Called when paste starts
   *
   * @param element
   */
  protected onStart(element: Element): void {
    const title = `${TYPO3.lang['paste.modal.title.paste'] || 'Paste record'}: "${$(element).data('title')}"`;
    const severity = top.TYPO3.Severity[$(element).data('severity')] || top.TYPO3.Severity.info;
    const buttons: object[] = [
      {
        active: true,
        btnClass: 'btn-default',
        text: TYPO3.lang['paste.modal.button.cancel'] || 'Cancel',
        trigger: () => Modal.currentModal.trigger('modal-dismiss'),
      },
    ];

    let content;

    if ($(element).is(Paste.identifier.mode.copy)) {
      content = TYPO3.lang['paste.modal.pastecopy'] || 'Do you want to copy the record to this position?';
      buttons.push({
        btnClass: 'btn-' + Severity.getCssClass(severity),
        text: TYPO3.lang['paste.modal.button.pastecopy'] || 'Copy',
        trigger: () => this.onFinish(element),
      });
    } else {
      content = TYPO3.lang['paste.modal.paste'] || 'Do you want to move the record to this position?';
      buttons.push({
        btnClass: 'btn-' + Severity.getCssClass(severity),
        text: TYPO3.lang['paste.modal.button.paste'] || 'Move',
        trigger: () => this.onFinish(element),
      });
    }

    Modal.show(title, content, severity, buttons);
  }
}

export = new Paste();

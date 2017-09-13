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
 * Module: TYPO3/CMS/Grid/Paste
 * this JS code does the paste logic for the grid layout component
 * based on jQuery UI
 */
define(['jquery',
        'TYPO3/CMS/Grid/DragDrop',
        'TYPO3/CMS/Backend/Modal',
        'TYPO3/CMS/Backend/Severity'
       ], function ($, DragDrop, Modal, Severity) {
	'use strict';

	/**
	 *
	 * @type {{}}
	 * @exports TYPO3/CMS/Grid/Paste
	 */
	var Paste = {
		openedPopupWindow: []
	};

	/**
	 * initializes paste icons for all content elements on the page
	 */
	Paste.initialize = function () {
        $(this).find('.t3js-paste').on('click', function (evt) {
            evt.preventDefault();
            Paste.activatePasteModal($(this));
        });
	};

	/**
	 * generates the paste into / paste after modal
	 */
	Paste.activatePasteModal = function (element) {
		var $element = $(element);
		var url = $element.data('url') || null;
		var title = (TYPO3.lang['paste.modal.title.paste'] || 'Paste record') + ': "' + $element.data('title') + '"';
		var severity = (typeof top.TYPO3.Severity[$element.data('severity')] !== 'undefined') ? top.TYPO3.Severity[$element.data('severity')] : top.TYPO3.Severity.info;
		if ($element.hasClass('t3js-paste-copy')) {
			var content = TYPO3.lang['paste.modal.pastecopy'] || 'Do you want to copy the record to this position?';
			var buttons = [
				{
					text: TYPO3.lang['paste.modal.button.cancel'] || 'Cancel',
					active: true,
					btnClass: 'btn-default',
					trigger: function () {
						Modal.currentModal.trigger('modal-dismiss');
					}
				},
				{
					text: TYPO3.lang['paste.modal.button.pastecopy'] || 'Copy',
					btnClass: 'btn-' + Severity.getCssClass(severity),
					trigger: function () {
						Modal.currentModal.trigger('modal-dismiss');
						DragDrop.onDrop($element, $element, null);
					}
				}
			];
		} else {
			var content = TYPO3.lang['paste.modal.paste'] || 'Do you want to move the record to this position?';
			var buttons = [
				{
					text: TYPO3.lang['paste.modal.button.cancel'] || 'Cancel',
					active: true,
					btnClass: 'btn-default',
					trigger: function () {
						Modal.currentModal.trigger('modal-dismiss');
					}
				},
				{
					text: TYPO3.lang['paste.modal.button.paste'] || 'Move',
					btnClass: 'btn-' + Severity.getCssClass(severity),
					trigger: function () {
						Modal.currentModal.trigger('modal-dismiss');
						DragDrop.onDrop($element, $element, null);
					}
				}
			];
		}
		if (url !== null) {
			var separator = (url.indexOf('?') > -1) ? '&' : '?';
			var params = $.param({data: $element.data()});
			Modal.loadUrl(title, severity, buttons, url + separator + params);
		} else {
			Modal.show(title, content, severity, buttons);
		}
	}

	$(Paste.initialize);
	return Paste;
});

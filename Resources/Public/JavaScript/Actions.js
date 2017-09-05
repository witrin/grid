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
 * Module: TYPO3/CMS/Grid/Actions
 * JavaScript implementations for page actions
 */
define(['jquery', 'TYPO3/CMS/Backend/Storage'], function($, Storage) {
	'use strict';

	/**
	 *
	 * @type {{settings: {pageId: number, language: {pageOverlayId: number}}, identifier: {pageTitle: string, hiddenElements: string}, elements: {$pageTitle: null, $showHiddenElementsCheckbox: null}, documentIsReady: boolean}}
	 * @exports TYPO3/CMS/Grid/Actions
	 */
	var Actions = {
		settings: {
		},
		identifier: {
			gridContainer: '.t3js-content-container',
			showHiddenContent: '.t3js-hidden-content-toggle',
			hiddenContent: '.t3js-hidden-content',

		},
		elements: {
			$showHiddenContentCheckbox: null
		},
		documentIsReady: false
	};

	/**
	 * Initialize elements
	 */
	Actions.initializeElements = function() {
		Actions.elements.$gridContainer = $(Actions.identifier.gridContainer);
		Actions.elements.$showHiddenContentCheckbox = $(Actions.identifier.showHiddenContent);
	};

	/**
	 * Initialize events
	 */
	Actions.initializeEvents = function() {
		Actions.elements.$showHiddenContentCheckbox.on('change', Actions.toggleContentElementVisibility);
	};

	/**
	 * Toggles the "Show hidden content" checkbox
	 */
	Actions.toggleContentElementVisibility = function() {
		var $me = $(this),
			$hiddenContent = $(Actions.identifier.hiddenContent),
			tca = JSON.parse(Actions.elements.$gridContainer.attr('data-tca'));

		// show a spinner to show activity
		var $spinner = $('<span />', {class: 'checkbox-spinner fa fa-circle-o-notch fa-spin'});
		$me.hide().after($spinner);

		if ($me.prop('checked')) {
			$hiddenContent.slideDown();
		} else {
			$hiddenContent.slideUp();
		}

		Storage.Persistent.set(
			'tx_grid.' + tca.container.table + '.' + tca.container.field + '.showHiddenContent',
			$me.prop('checked') ? 1 : 0
		).done(function() {
			$spinner.remove();
			$me.show();
		});
	};

	$(function() {
		Actions.initializeElements();
		Actions.initializeEvents();
	});

	return Actions;
});

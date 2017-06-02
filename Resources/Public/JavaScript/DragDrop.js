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
 * Module: TYPO3/CMS/Grid/DragDrop
 * this JS code does the drag+drop logic for the Grid Component
 * based on jQuery UI
 */
define(['jquery', 'jquery-ui/droppable'], function($) {
	'use strict';

	/**
	 *
	 * @type {{containerIdentifier: string, contentIdentifier: string, dragIdentifier: string, dragHeaderIdentifier: string, dropZoneIdentifier: string, columnIdentifier: string, validDropZoneClass: string, dropPossibleHoverClass: string, addContentIdentifier: string, originalStyles: string}}
	 * @exports TYPO3/CMS/Backend/LayoutModule/DragDrop
	 */
	var DragDrop = {
		containerIdentifier: '.t3js-content-container',
		contentIdentifier: '.t3js-content-element',
		dragIdentifier: '.t3-content-element-drag-item',
		dragHeaderIdentifier: '.t3js-content-element-drag-handle',
		dropZoneIdentifier: '.t3js-content-element-drop-zone',
		columnIdentifier: '.t3js-backend-layout-column',
		dropZoneClass: 't3-content-element-drop-zone',
		dropZoneHoverClass: 't3-content-element-drop-zone-hover',
		addContentIdentifier: '.t3js-new-content-element',
		clone: true
	};

	/**
	 * initializes Drag+Drop for all content elements on the container
	 */
	DragDrop.initialize = function() {
		$(DragDrop.contentIdentifier).each(function() {
			$(this).draggable({
				handle: this.dragHeaderIdentifier,
				scope: $(DragDrop.containerIdentifier).data('table'),
				cursor: 'move',
				distance: 20,
				addClasses: 'active-drag',
				revert: 'invalid',
				zIndex: 100,
				helper : 'clone',
				appendTo: 'body',
				start: function (event, ui) {
					DragDrop.onDragStart($(this), $(ui.helper));
				},
				stop: function (event, ui) {
					DragDrop.onDragStop($(this), $(ui.helper));
				}
			})
		});

		$(DragDrop.dropZoneIdentifier).each(function() {
			$(this).droppable({
				accept: this.contentIdentifier,
				scope: $(DragDrop.containerIdentifier).data('table'),
				tolerance: 'pointer',
				over: function (event, ui) {
					DragDrop.onDropHoverOver($(ui.draggable), $(this));
				},
				out: function (event, ui) {
					DragDrop.onDropHoverOut($(ui.draggable), $(this));
				},
				drop: function (event, ui) {
					DragDrop.onDrop($(ui.draggable), $(this), event);
				}
			})
		});
	};

	/**
	 * called when a draggable is selected to be moved
	 * @param $element a jQuery object for the draggable
	 * @private
	 */
	DragDrop.onDragStart = function($element, $clone) {

		// Setup the clone
		$clone.css('width', $element.outerWidth());
		// Hide the original
		$element.css('visibility', 'hidden');

		$clone.append('<div class="ui-draggable-copy-message">' + TYPO3.lang['dragdrop.copy.message'] + '</div>');

		// Hide create new element button
		$(DragDrop.containerIdentifier).find(DragDrop.addContentIdentifier).hide();

		// Make the drop zones visible
		$(DragDrop.dropZoneIdentifier).not('[data-relation=' + $element.data('uid') + ']').each(function () {
			$(this).addClass(DragDrop.dropZoneClass);
		});
	};

	/**
	 * called when a draggable is released
	 * @param $element a jQuery object for the draggable
	 * @private
	 */
	DragDrop.onDragStop = function($element, $clone) {
		// Show create new element button
		$(DragDrop.containerIdentifier).find(DragDrop.addContentIdentifier).show();
		$element.find(DragDrop.dropZoneIdentifier).show();
		$element.find('.ui-draggable-copy-message').remove();

		// Reset inline style
		$element.attr('style', null);

		$(DragDrop.dropZoneIdentifier + '.' + DragDrop.dropZoneClass).removeClass(DragDrop.dropZoneClass);
	};

	/**
	 * adds CSS classes when hovering over a dropzone
	 * @param $draggable
	 * @param $droppable
	 * @private
	 */
	DragDrop.onDropHoverOver = function($draggable, $droppable) {
		if ($droppable.hasClass(DragDrop.dropZoneClass)) {
			$droppable.addClass(DragDrop.dropZoneHoverClass);
		}
	};

	/**
	 * removes the CSS classes after hovering out of a dropzone again
	 * @param $draggable
	 * @param $droppable
	 * @private
	 */
	DragDrop.onDropHoverOut = function($draggable, $droppable) {
		$droppable.removeClass(DragDrop.dropZoneHoverClass);
	};

	/**
	 * this method does the whole logic when a draggable is dropped on to a dropzone
	 * sending out the request and afterwards move the HTML element in the right place.
	 *
	 * @param $draggable
	 * @param $droppable
	 * @param {Event} event the event
	 * @private
	 */
	DragDrop.onDrop = function($draggable, $droppable, event) {
		$droppable.removeClass(DragDrop.dropZoneHoverClass);
		$('[data-relation=' + $draggable.data('uid') + ']').insertAfter($droppable);

		var container = $droppable.closest(DragDrop.containerIdentifier);
		var element = parseInt($draggable.data('uid'));
		var tca = $droppable.closest('[data-tca]').data('tca');
		var language = parseInt($droppable.closest('[data-language]').data('language'));
		var area = $droppable.closest(DragDrop.columnIdentifier).data('uid');
		var target = parseInt($droppable.attr('data-target'));
		var data = {};
		var parameters = {
			cmd: {},
			data: {}
		};

		data.pid = container.data('pid');
		data[tca.element.fields.area] = area;
		data[tca.element.fields.language] = language;
		data[tca.element.fields.foreign.field] = container.data('uid');

		if (tca.element.fields.foreign.table) {
			data[tca.element.fields.foreign.table] = tca.container.table;
		}

		parameters.cmd[tca.element.table] = {};
		parameters.data[tca.element.table] = {};

		if (element > 0) {

			if (event && event.originalEvent.ctrlKey) {
				parameters.cmd[tca.element.table][element] = {
					copy: {
						action: 'paste',
						target: target, // @todo not sure about this regarding foreign_field
						update: {}
					}
				};
				parameters.cmd[tca.element.table][element].copy.update = data;
			} else {
				parameters.data[tca.element.table][element] = {};
				parameters.data[tca.element.table][element] = data;
				parameters.cmd[tca.element.table][element] = {move: target};
			}
		} else {
			// @todo What about `rootLevel !== 0`?
			parameters.data[tca.element.table].NEW = $draggable.data('values');
			$.extend(parameters.data[tca.element.table].NEW, data);
		}
		console.log(parameters);
		// fire the request, and show a message if it has failed
		DragDrop.ajaxAction($droppable, $draggable, parameters);
	};

	/**
	 * this method does the actual AJAX request for both, the  move and the copy action.
	 *
	 * @param $droppable
	 * @param $draggable
	 * @param parameters
	 * @param copyAction
	 * @private
	 */
	DragDrop.ajaxAction = function($droppable, $draggable, parameters) {
		require(['TYPO3/CMS/Backend/AjaxDataHandler'], function (DataHandler) {
			DataHandler.process(parameters).done(function (result) {
				if (!result.hasErrors) {
					// insert draggable on the new position
					if (!$droppable.parent().hasClass(DragDrop.contentIdentifier.substring(1))) {
						$draggable.detach().css({top: 0, left: 0})
							.insertAfter($droppable.closest(DragDrop.dropZoneIdentifier));
					} else {
						$draggable.detach().css({top: 0, left: 0})
							.insertAfter($droppable.closest(DragDrop.contentIdentifier));
					}
					// @todo Update without page refresh by using Ajax for the content preview rendering
					// should be always reloaded otherwise the history back of the browser doesn't work correctly
					self.location.reload(true);
				}
			});
		});
	};

	$(DragDrop.initialize);
	return DragDrop;
});

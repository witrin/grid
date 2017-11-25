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
import 'jquery-ui/draggable';
import 'jquery-ui/droppable';
import AjaxDataHandler = require('TYPO3/CMS/Backend/AjaxDataHandler');

/**
 * Drag type
 */
enum DragType {
  Record,
  Preset,
}

/**
 * Handle drag and drop of records and presets within a grid container
 *
 * @exports TYPO3/CMS/Grid/DragDrop
 * @todo Support multiple grid container
 */
class DragDrop {

  protected static readonly identifier = {
    area: '.t3js-grid-drag-drop-area',
    container: '.t3js-grid-drag-drop-container',
    handle: '.t3js-grid-drag-drop-handle',
    item: '.t3js-grid-drag-drop-item',
    zone: '.t3js-grid-drag-drop-zone',
  };

  protected static readonly styles = {
    container: {
      active: 't3-grid-drag-drop-active',
    },
    zone: {
      active: 't3-grid-drag-drop-zone-active',
      hover: 't3-grid-drag-drop-zone-hover',
    },
  };

  /**
   * Initialize drag and drop for all records and presets on the grid container
   */
  public initialize(): void {
    const table = 'pages';

    // wire draggables
    $(DragDrop.identifier.item).each((index: number, element: Element) => {
      $(element).draggable({
        addClasses: true,
        appendTo: 'body',
        cursor: 'move',
        distance: 20,
        handle: DragDrop.identifier.handle,
        helper: 'clone',
        revert: 'invalid',
        scope: table,
        start: (event: Event, ui: any) => this.onDragStart(element, ui.helper),
        stop: (event: Event, ui: any) => this.onDragStop(element, ui.helper),
        zIndex: 100,
      });
    });
    // wire droppables
    $(DragDrop.identifier.zone).each((index: number, element: Element) => {
      $(element).droppable({
        accept: ($draggable: JQuery) => this.isDropAllowed(element, $draggable.get(0)),
        activeClass: DragDrop.styles.zone.active,
        drop: (event: Event, ui: any) => this.onDrop(ui.draggable, element, event),
        hoverClass: DragDrop.styles.zone.hover,
        scope: table,
        tolerance: 'pointer',
      });
    });
  }

  /**
   * Check if draggable can be dropped on given droppable
   *
   * @param droppable
   * @param draggable
   */
  protected isDropAllowed(zone: Element, item: Element) {
    const target = Math.abs(Number($(zone).data('target')));
    const $area = $(zone).closest(DragDrop.identifier.area);
    const $content = $area.find(`${DragDrop.identifier.item}[data-uid=${target}]`);
    const $siblings = $area.find(DragDrop.identifier.item);
    // only allowed if item is not a record or zone is not a direct sibling of the item
    return this.getDragType(item) === DragType.Preset ||
      $siblings.index($(item)) < 0 ||
      $siblings.index($(item)) - 1 !== $siblings.index($content) &&
      $siblings.index($(item)) !== $siblings.index($content);
  }

  /**
   * Called when draggable is selected to be moved
   *
   * @param item Draggable element
   * @param clone Draggable element clone
   */
  protected onDragStart(item: Element, clone: Element): void {
    // update container status
    $(DragDrop.identifier.container).addClass(DragDrop.styles.container.active);
    // prepare if item is a record
    if (this.getDragType(item) === DragType.Record) {
      $(clone).css('width', $(item).outerWidth());
      $(item).css('visibility', 'hidden');
      $(clone).append(`<div class="ui-draggable-copy-message">${TYPO3.lang['dragdrop.copy.message']}</div>`);
    }
  }

  /**
   * Called when a draggable is released
   *
   * @param item Draggable element
   * @param clone Draggable element clone
   */
  protected onDragStop(item: Element, clone: Element): void {
    // update container status
    $(DragDrop.identifier.container).removeClass(DragDrop.styles.container.active);
    // prepare if item is a record
    if (this.getDragType(item) === DragType.Record) {
      $(item).attr('style', null);
    }
  }

  /**
   * Called when a draggable is dropped
   *
   * @param item Draggable element
   * @param zone Droppable element
   * @param event Drop event
   * @todo Handle exceptions
   */
  protected onDrop(item: Element, zone: Element, event: any): void {
    const uid = parseInt($(item).data('uid'), 10);
    const target = parseInt($(zone).data('target'), 10);
    const parameters = $(zone).data('parameters');

    let data = $.extend(true, {}, parameters.defaults, parameters.overrides);
    let query: object;

    if (this.getDragType(item) === DragType.Record) {
      // check requested action
      if (event && event.originalEvent.ctrlKey) {
        query = {
          cmd: { [parameters.table]: { [uid]: { copy: { action: 'paste', target, update: data } } } },
        };
      } else {
        query = {
          cmd: { [parameters.table]: { [uid]: { move: target } } },
          data: { [parameters.table]: { [uid]: data } },
        };
      }
    } else {
      // @todo What about `rootLevel !== 0`?
      const values = $(item).data('values');

      data = $.extend(true, {}, data, values.defaults, values.overrides);
      query = { data: { [parameters.table]: { NEW: data } } };
    }

    AjaxDataHandler.process(query).then((result) => {
      if (this.getDragType(item) === DragType.Record) {
        $(item).parent().detach().insertAfter(
          $(zone).parentsUntil(DragDrop.identifier.area).addBack().get(0),
        );
      } else {
        $(item).clone().wrap('<div class="t3-content-wrapper"></div>').insertAfter(
          $(zone).parentsUntil(DragDrop.identifier.area).addBack().get(0),
        );
      }
      self.location.reload(true);
    }).catch((error) => {
      console.log(error);
    });
  }

  /**
   * Get type of draggable
   *
   * @param item Draggable element
   */
  protected getDragType(item: Element): DragType {
    return $(item).is('[data-uid]') ? DragType.Record : DragType.Preset;
  }
}

export = new DragDrop();

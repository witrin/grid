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
import 'jquery-ui/resizable';

/**
 * Toggle states
 */
enum ToggleState {
  Collapsed = 'collapsed',
  Expanded = 'expanded',
  FullExpanded = 'full-expanded',
}

/**
 * Handle toggling of a backend module sidebar
 *
 * @exports TYPO3/CMS/Grid/Sidebar
 * @todo Move to sysext `backend`
 */
class Sidebar {

  /**
   * Identifier for sidebar elements
   */
  protected static readonly identifier: { [key: string]: string } = {
    element: '.t3js-sidebar',
    module: '.module, .module-docheader',
    panel: '.t3js-sidebar-panel',
    split: '.t3js-sidebar-split',
    toggle: '.t3js-sidebar-toggle',
  };

  /**
   * Get toggle state
   *
   * @param start
   * @param offset
   */
  protected static getState(start: string, offset: number): string {
    const values: string[] = Object.keys(ToggleState).map((k) => ToggleState[k as any]);
    const n = values.length;

    return values[(($.inArray(start, values) + offset) % n + n) % n];
  }

  /**
   * Initialize sidebar
   */
  public initialize(): void {
    const $element = $(Sidebar.identifier.element);
    const $toggle = $(Sidebar.identifier.toggle);

    $toggle.on('click', (event: Event) => this.onToggle());
    $(window).on('resize', (event: Event) => this.onUpdate());

    $(Sidebar.identifier.split).css({ right: this.calculateOffset() });
    $toggle.css({ visibility: 'visible' });

    if ($element.attr('data-resizable') !== undefined) {
      $element.resizable({
        handles: {
          w: Sidebar.identifier.split,
        },
        resize: (event: Event, ui: any) => this.onResize(),
        start: (event: Event, ui: any) => this.onStart(),
      });

      $.each(['minWidth', 'maxWidth'], (index, option) => {
        if ($element.data().hasOwnProperty(option)) {
          $element.resizable('option', option, $element.data(option));
        }
      });
    }

    this.onUpdate();
  }

  /**
   * Called when sidebar needs update
   */
  protected onUpdate(): void {
    $(Sidebar.identifier.panel).css('height', $(window).height() + 'px');
  }

  /**
   * Called when sidebar toggles
   */
  protected onToggle(): void {
    const $element = $(Sidebar.identifier.element);
    const state = $element.attr('data-toggle');

    $element.attr('data-toggle', Sidebar.getState(state, 1));
    $element.removeAttr('style');

    if (Sidebar.getState(state, 2) === ToggleState.Collapsed) {
      $element.removeAttr('data-expandable');
    } else {
      $element.attr('data-expandable', '');

      if ($element.attr('data-size')) {
        $element.css('width', $element.attr('data-size'));
      }
    }

    $(Sidebar.identifier.module).css('width', 'calc( 100% - ' + this.calculateWidth() + 'px )');
  }

  /**
   * Called on sidebar resize
   */
  protected onResize(): void {
    const $element = $(Sidebar.identifier.element);

    $element.removeAttr('data-collapsed');
    $element.attr('data-toggle', Sidebar.getState(ToggleState.Collapsed, -1));
  }

  /**
   * Called on sidebar start
   */
  protected onStart(): void {
    const width = this.calculateWidth();

    // See https://bugs.jqueryui.com/ticket/4985
    $(this).css('left', '');
    $(this).attr('data-size', width);

    $(Sidebar.identifier.module).css('width', 'calc( 100% - ' + width + 'px )');
    $(Sidebar.identifier.split).css('right', this.calculateOffset());
  }

  /**
   * Calculate sidebar width
   *
   * @returns number
   */
  protected calculateWidth(): number {
    return $(Sidebar.identifier.element).outerWidth();
  }

  /**
   * Calculate sidebar offset
   *
   * @returns number
   */
  protected calculateOffset(): number {
    const $module = $(Sidebar.identifier.module);

    return $module.get(0).offsetWidth - $module.get(0).clientWidth + $(Sidebar.identifier.split).outerWidth();
  }
}

export = new Sidebar();

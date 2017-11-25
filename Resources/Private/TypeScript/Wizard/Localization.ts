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

import 'bootstrap';
import $ = require('jquery');
import DataHandler = require('TYPO3/CMS/Backend/AjaxDataHandler');
import Icons = require('TYPO3/CMS/Backend/Icons');
import Severity = require('TYPO3/CMS/Backend/Severity');
import Wizard = require('TYPO3/CMS/Backend/Wizard');

/**
 * Locate a grid area
 */
interface AreaLocator {
  containerUid: number;
  areaUid: number;
  languageUid: number;
  containerTable: number;
  containerField: string;
}

/**
 * Wizard for localization workflow
 *
 * @exports TYPO3/CMS/Grid/Wizard/Localization
 */
class Localization {

  /**
   * Identifier for markup
   */
  protected static readonly identifier = {
    action: '.t3js-grid-localize',
  };

  /**
   * Available options
   */
  protected options: any = {
    copy: {
      description: 'localize.educate.copy',
      icon: 'actions-localize',
      label: 'Copy',
      value: 'copyFromLanguage',
    },
    translate: {
      description: 'localize.educate.translate',
      icon: 'actions-edit-copy',
      label: 'Translate',
      value: 'localize',
    },
  };

  /**
   * Current settings
   */
  protected settings = {
    area: null as AreaLocator,
    language: null as string,
    mode: null as string,
    records: null as number[],
  };

  /**
   * Initialize all available actions
   */
  public initialize(): void {
    // load option icons
    $.when(Object.keys(this.options).map((option) => {
      const deferred = $.Deferred();
      Icons.getIcon(this.options[option].icon, Icons.sizes.large).done((markup) => {
        this.options[option].icon = markup;
        deferred.resolve();
      });
      return deferred;
    })).done(() => $(Localization.identifier.action).prop('disabled', false));

    // wire click handler
    $(document).on('click', Localization.identifier.action, (event: any) => {
      const $target: JQuery = $(event.currentTarget);
      const options: string = Object.keys(this.options).filter(
        (option) => $target.is(`[data-allow-${option}]`),
      ).map((option) => `
        <div class="row">
          <div class="btn-group col-sm-3">
            <label class="btn btn-block btn-default t3js-option"
              data-helptext=".t3js-helptext-translate">
              ${this.options[option].icon}
              <input id="mode_copy" name="mode" style="display: none;"
                type="radio" value="${this.options[option].value}" /><br />${this.options[option].label}
            </label>
          </div>
          <div class="col-sm-9">
            <p class="t3js-helptext t3js-helptext-translate text-muted">
              ${TYPO3.lang[this.options[option].description]}
            </p>
          </div>
        </div>
      `).join('<hr />');

      // initialize settings
      this.settings.records = [];
      this.settings.area = {
        areaUid: $target.data('areaUid'),
        containerField: $target.data('containerField'),
        containerTable: $target.data('containerTable'),
        containerUid: $target.data('containerUid'),
        languageUid: $target.data('languageUid'),
      };

      // initialize steps
      Wizard.addSlide(
        'localize-choose-action',
        TYPO3.lang['localize.wizard.header'].replace(
          '{0}',
          $target.data('areaTitle'),
        ).replace(
          '{1}',
          $target.data('languageTitle'),
        ),
        `<div data-toggle="buttons">${options}</div>`,
        Severity.info,
      );

      Wizard.addSlide(
        'localize-choose-language',
        TYPO3.lang['localize.view.chooseLanguage'],
        '',
        Severity.info,
        ($slide: JQuery) => this.onSelectLanguage($slide),
      );

      Wizard.addSlide(
        'localize-summary',
        TYPO3.lang['localize.view.summary'],
        '',
        Severity.info,
        ($slide: JQuery) => this.onSelectRecords($slide),
      );

      Wizard.addFinalProcessingSlide(() => this.onFinish()).done((result: any) => this.onStart());
    });
  }

  /**
   * Called when the wizard starts
   */
  protected onStart(): void {
    Wizard.show();
    // wire click handler for options
    Wizard.getComponent().on('click', '.t3js-option', (event: any) => {
      const $radio = $(event.currentTarget).find('input[type="radio"]');

      if ($(event.currentTarget).data('helptext')) {
        const $container = $(event.delegateTarget);
        $container.find('.t3js-helptext').addClass('text-muted');
        $container.find($(event.currentTarget).data('helptext')).removeClass('text-muted');
      }
      if ($radio.length > 0) {
        this.settings.mode = $radio.val();
      }
      Wizard.unlockNextStep();
    });
  }

  /**
   * Called when the source language has to be selected
   *
   * @param $slide
   */
  protected onSelectLanguage($slide: JQuery): void {
    Icons.getIcon('spinner-circle-dark', Icons.sizes.large).done((markup: any) => {
      $slide.html(`<div class="text-center">${markup}</div>`);

      $.ajax({
        data: this.settings.area,
        url: TYPO3.settings.ajaxUrls.grid_area_languages,
      }).done((result: any) => {
        if (result.length === 1) {
          // we only have one result, auto select the record and continue
          this.settings.language = result[0].uid as string;
          Wizard.unlockNextStep().trigger('click');
        } else {
          const $languageButtons = $('<div />', { 'class': 'row', 'data-toggle': 'buttons' });

          $.each(result, (_, languageObject) => {
            $languageButtons.append(`
                <div class="col-sm-4">
                  <label class="btn btn-default btn-block t3js-option option">
                    <input id="language${languageObject.uid}" name="language"
                      style="display: none;" type="radio" value="${languageObject.uid}" />
                    ${languageObject.flagIcon.html()}
                    ${languageObject.title}
                  </label>
                </div>
            `);
          });
          $slide.html($languageButtons.html());
        }
      });
    });
  }

  /**
   * Called when the records to localize have to be selected
   *
   * @param {JQuery} $slide
   */
  protected onSelectRecords($slide: JQuery): void {
    Icons.getIcon('spinner-circle-dark', Icons.sizes.large).done((markup: string) => {
      $slide.html(`<div class="text-center">${markup}</div>`);

      $.ajax({
        data: $.extend({}, this.settings.area, {
          destinationLanguageUid: this.settings.area.languageUid,
          languageUid: this.settings.language,
        }),
        url: TYPO3.settings.ajaxUrls.records_localize_summary,
      }).done((result: any) => {
        const $summary = $('<div />', { class: 'row' });

        $.each(result, (index, record) => {
          const label = ' (' + record.uid + ') ' + record.title;
          this.settings.records.push(record.uid);

          $summary.append(`
            <div class="col-sm-6">
              <div class="input-group">
                <span class="input-group-addon">
                  <input type="checkbox" id="record-uid-${record.uid}" checked
                    data-uid="${record.uid}" aria-label="${label}" />
                </span>
                <label class="form-control" for="record-uid-${record.uid}">
                  ${record.icon}
                  ${label}
                </label>
              </div>
            </div>
          `);
        });
        $slide.html($summary.html());
        Wizard.unlockNextStep();

        Wizard.getComponent().on('change', 'input[type="checkbox"]', (event: Event) => {
          const uid = $(event.currentTarget).data('uid');

          if ($(event.currentTarget).is(':checked')) {
            this.settings.records.push(uid);
          } else {
            const index = this.settings.records.indexOf(uid);
            if (index > -1) {
              this.settings.records.splice(index, 1);
            }
          }

          if (this.settings.records.length > 0) {
            Wizard.unlockNextStep();
          } else {
            Wizard.lockNextStep();
          }
        });
      });
    });
  }

  /**
   * Called when the wizard finishes
   */
  protected onFinish(): void {
    $.ajax({
      data: $.extend({}, this.settings.area, {
        action: this.settings.mode,
        contentUids: this.settings.records,
        destinationLanguageUid: this.settings.area.languageUid,
        sourceLanguageUid: this.settings.language,
      }),
      url: TYPO3.settings.ajaxUrls.records_localize,
    }).done(() => {
      Wizard.dismiss();
      document.location.reload();
    });
  }
}

export = new Localization();

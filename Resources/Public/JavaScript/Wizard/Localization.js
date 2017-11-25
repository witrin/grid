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
define(["require", "exports", "jquery", "TYPO3/CMS/Backend/Icons", "TYPO3/CMS/Backend/Severity", "TYPO3/CMS/Backend/Wizard", "bootstrap"], function (require, exports, $, Icons, Severity, Wizard) {
    "use strict";
    /**
     * Wizard for record localization workflow
     *
     * @exports TYPO3/CMS/Grid/Wizard/Localization
     */
    var Localization = (function () {
        function Localization() {
            this.options = {
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
            this.settings = {};
            this.records = [];
        }
        Localization.prototype.initialize = function () {
            var _this = this;
            // load option icons
            $.when(Object.keys(this.options).map(function (option) {
                var deferred = $.Deferred();
                Icons.getIcon(_this.options[option].icon, Icons.sizes.large).done(function (markup) {
                    _this.options[option].icon = markup;
                    deferred.resolve();
                });
                return deferred;
            })).done(function () { return $(Localization.identifier.triggerButton).prop('disabled', false); });
            // wire click handler
            $(document).on('click', Localization.identifier.triggerButton, function (event) {
                var $target = $(event.currentTarget);
                var options = Object.keys(_this.options).filter(function (option) { return $target.is("[data-allow-" + option + "]"); }).map(function (option) { return "\n        <div class=\"row\">\n          <div class=\"btn-group col-sm-3\">\n            <label class=\"btn btn-block btn-default t3js-option\"\n              data-helptext=\".t3js-helptext-translate\">\n              " + _this.options[option].icon + "\n              <input id=\"mode_copy\" name=\"mode\" style=\"display: none;\"\n                type=\"radio\" value=\"" + _this.options[option].value + "\" /><br />" + _this.options[option].label + "\n            </label>\n          </div>\n          <div class=\"col-sm-9\">\n            <p class=\"t3js-helptext t3js-helptext-translate text-muted\">\n              " + TYPO3.lang[_this.options[option].description] + "\n            </p>\n          </div>\n        </div>\n      "; }).join('<hr />');
                _this.records = [];
                _this.area = {
                    areaUid: $target.data('areaUid'),
                    containerField: $target.data('containerField'),
                    containerTable: $target.data('containerTable'),
                    containerUid: $target.data('containerUid'),
                    languageUid: $target.data('languageUid'),
                };
                Wizard.addSlide('localize-choose-action', TYPO3.lang['localize.wizard.header'].replace('{0}', $target.data('areaTitle')).replace('{1}', $target.data('languageTitle')), "<div data-toggle=\"buttons\">" + options + "</div>", Severity.info);
                Wizard.addSlide('localize-choose-language', TYPO3.lang['localize.view.chooseLanguage'], '', Severity.info, function ($slide) { return _this.onSelectLanguage($slide); });
                Wizard.addSlide('localize-summary', TYPO3.lang['localize.view.summary'], '', Severity.info, function ($slide) { return _this.onSelectRecords($slide); });
                Wizard.addFinalProcessingSlide(function () {
                    $.ajax({
                        data: $.extend({}, _this.area, {
                            action: _this.settings.mode,
                            contentUids: _this.records,
                            destinationLanguageUid: _this.area.languageUid,
                            sourceLanguageUid: _this.settings.language,
                        }),
                        url: TYPO3.settings.ajaxUrls['records_localize'],
                    }).done(function () {
                        Wizard.dismiss();
                        document.location.reload();
                    });
                }).done(function (result) {
                    Wizard.show();
                    Wizard.getComponent().on('click', '.t3js-option', function (event) {
                        var $radio = $(event.currentTarget).find('input[type="radio"]');
                        if ($(event.currentTarget).data('helptext')) {
                            var $container = $(event.delegateTarget);
                            $container.find('.t3js-helptext').addClass('text-muted');
                            $container.find($(event.currentTarget).data('helptext')).removeClass('text-muted');
                        }
                        if ($radio.length > 0) {
                            _this.settings[$radio.attr('name')] = $radio.val();
                        }
                        Wizard.unlockNextStep();
                    });
                });
            });
        };
        Localization.prototype.onSelectLanguage = function ($slide) {
            var _this = this;
            Icons.getIcon('spinner-circle-dark', Icons.sizes.large).done(function (markup) {
                $slide.html("<div class=\"text-center\">" + markup + "</div>");
                $.ajax({
                    data: _this.area,
                    url: TYPO3.settings.ajaxUrls['grid_area_languages'],
                }).done(function (result) {
                    if (result.length === 1) {
                        // We only have one result, auto select the record and continue
                        _this.settings.language = result[0].uid + ''; // we need a string
                        Wizard.unlockNextStep().trigger('click');
                        return;
                    }
                    var $languageButtons = $('<div />', { 'class': 'row', 'data-toggle': 'buttons' });
                    $.each(result, function (_, languageObject) {
                        $languageButtons.append("\n              <div class=\"col-sm-4\">\n                <label class=\"btn btn-default btn-block t3js-option option\">\n                  <input id=\"language" + languageObject.uid + "\" name=\"language\"\n                    style=\"display: none;\" type=\"radio\" value=\"" + languageObject.uid + "\" />\n                  " + languageObject.flagIcon.html() + "\n                  " + languageObject.title + "\n                </label>\n              </div>\n          ");
                    });
                    $slide.html($languageButtons.html());
                });
            });
        };
        Localization.prototype.onSelectRecords = function ($slide) {
            var _this = this;
            Icons.getIcon('spinner-circle-dark', Icons.sizes.large).done(function (markup) {
                $slide.html("<div class=\"text-center\">" + markup + "</div>");
                $.ajax({
                    data: $.extend({}, _this.area, {
                        destinationLanguageUid: _this.area.languageUid,
                        languageUid: _this.settings.language,
                    }),
                    url: TYPO3.settings.ajaxUrls['records_localize_summary'],
                }).done(function (result) {
                    var $summary = $('<div />', { class: 'row' });
                    $.each(result, function (index, record) {
                        var label = ' (' + record.uid + ') ' + record.title;
                        _this.records.push(record.uid);
                        $summary.append("\n            <div class=\"col-sm-6\">\n              <div class=\"input-group\">\n                <span class=\"input-group-addon\">\n                  <input type=\"checkbox\" id=\"record-uid-" + record.uid + "\" checked\n                    data-uid=\"" + record.uid + "\" aria-label=\"" + label + "\" />\n                </span>\n                <label class=\"form-control\" for=\"record-uid-" + record.uid + "\">\n                  " + record.icon + "\n                  " + label + "\n                </label>\n              </div>\n            </div>\n          ");
                    });
                    $slide.html($summary.html());
                    Wizard.unlockNextStep();
                    Wizard.getComponent().on('change', 'input[type="checkbox"]', function (event) {
                        var uid = $(event.currentTarget).data('uid');
                        if ($(event.currentTarget).is(':checked')) {
                            _this.records.push(uid);
                        }
                        else {
                            var index = _this.records.indexOf(uid);
                            if (index > -1) {
                                _this.records.splice(index, 1);
                            }
                        }
                        if (_this.records.length > 0) {
                            Wizard.unlockNextStep();
                        }
                        else {
                            Wizard.lockNextStep();
                        }
                    });
                });
            });
        };
        /**
         * @type {{identifier: {triggerButton: string}, actions: {translate: $, copy: $}, settings: {}, records: []}}
         * @exports TYPO3/CMS/Grid/Localization
         */
        Localization.identifier = {
            triggerButton: '.t3js-localize',
        };
        return Localization;
    }());
    return new Localization();
});

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
 * Module: TYPO3/CMS/Wireframe/Wizard
 * this JS code does the form logic for the content element wizard
 * based on jQuery UI
 */
define(['jquery'], function($) {
    'use strict';

    var Wizard = {
        advancedContainerIdentifier: '.t3js-content-element-wizard-advanced',
        defaultContainerIdentifier: '.t3js-content-element-wizard-default',
        itemIdentifier: '.t3js-content-element-wizard-item',
        positionContainerIdentifier: '.t3js-content-element-wizard-positions',
        itemContainerIdentifier: '.t3js-content-element-wizard-items'
    };

    Wizard.initialize = function() {
        $(Wizard.advancedContainerIdentifier).each(function() {
            $(this).find(Wizard.itemIdentifier).on('click', function(event) {
                event.preventDefault();

                $(this).find('input').prop('checked', true);
                $(this).parents(Wizard.advancedContainerIdentifier).find(Wizard.positionContainerIdentifier)[0].scrollIntoView();
            });

            $(this).find(Wizard.positionContainerIdentifier).find('a').on('click', function(event) {
                event.preventDefault();

                window.location.href = $(this).parents('form').attr('action') +
                    $(this).parents(Wizard.advancedContainerIdentifier).find(Wizard.itemContainerIdentifier).find('input:checked').parents('[data-parameters]').attr('data-parameters') +
                    $(this).attr('data-parameters');
            });
        });

        $(Wizard.defaultContainerIdentifier).each(function() {
            $(this).find(Wizard.itemIdentifier).on('click', function(event) {
                event.preventDefault();

                window.location.href = $(this).parents('form').attr('action') + $(this).attr('data-parameters');
            });
        });
    };

    $(Wizard.initialize);

    return Wizard;
});

<?php

/**
 * Definitions for routes provided by EXT:grid
 */
return [
    // Get languages in grid area
    'grid_area_languages' => [
        'path' => '/grid/records/localize/get-languages',
        'target' => \TYPO3\CMS\Grid\Controller\LocalizationController::class . '::getLanguages'
    ],
    // Get summary of records to localize
    'records_localize_summary' => [
        'path' => '/grid/records/localize/summary',
        'target' => \TYPO3\CMS\Grid\Controller\LocalizationController::class . '::getSummary'
    ],
    // Localize the records
    'records_localize' => [
        'path' => '/grid/records/localize',
       'target' => \TYPO3\CMS\Grid\Controller\LocalizationController::class . '::localizeRecords'
    ]
];

<?php

/**
 * Definitions for routes provided by EXT:wireframe
 */
return [
    // Get languages in grid area
    'grid_area_languages' => [
        'path' => '/wireframe/records/localize/get-languages',
        'target' => \TYPO3\CMS\Wireframe\Controller\LocalizationController::class . '::getLanguages'
    ],
    // Get summary of records to localize
    'records_localize_summary' => [
        'path' => '/wireframe/records/localize/summary',
        'target' => \TYPO3\CMS\Wireframe\Controller\LocalizationController::class . '::getSummary'
    ],
    // Localize the records
    'records_localize' => [
        'path' => '/wireframe/records/localize',
       'target' => \TYPO3\CMS\Wireframe\Controller\LocalizationController::class . '::localizeRecords'
    ]
];

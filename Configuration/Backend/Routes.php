<?php

/**
 * Definitions for routes provided by EXT:grid
 */
return [
    // Register content preset controller
    'content_preset' => [
        'path' => '/grid/content/preset/',
        'target' => \TYPO3\CMS\Grid\Controller\ContentWizardController::class . '::processRequest'
    ],
    // Register new content element module
    'new_content_element' => [
        'path' => '/record/content/new',
        'target' => \TYPO3\CMS\Grid\Controller\ContentWizardController::class . '::processRequest'
    ]
];

<?php

/**
 * Definitions for routes provided by EXT:grid
 */
return [
    // Register content element controller
    'content_element' => [
        'path' => '/grid/content/',
        'target' => \TYPO3\CMS\Grid\Controller\ContentPresetController::class . '::processRequest'
    ],
    // Register new content element module
    'new_content_element' => [
        'path' => '/record/content/new',
        'target' => \TYPO3\CMS\Grid\Controller\ContentPresetController::class . '::processRequest'
    ]
];

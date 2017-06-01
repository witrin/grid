<?php

/**
 * Definitions for routes provided by EXT:wireframe
 */
return [
    // Register content element controller
    'content_element' => [
        'path' => '/wireframe/content/',
        'target' => \TYPO3\CMS\Wireframe\Controller\ContentPresetController::class . '::processRequest'
    ],
    // Register new content element module
    'new_content_element' => [
        'path' => '/record/content/new',
        'target' => \TYPO3\CMS\Wireframe\Controller\ContentPresetController::class . '::processRequest'
    ]
];

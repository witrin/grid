<?php

defined('TYPO3_MODE') or die();

// this is a hack and will be removed with https://review.typo3.org/51272
$GLOBALS['TCA']['pages_language_overlay']['columns']['content'] = [
    'config' => [
        'type' => 'inline',
        'foreign_table' => 'tt_content',
        'foreign_match_fields' => [
            'uid' => -1
        ],
        'grid_area_field' => 'colPos'
    ]
];
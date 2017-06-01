<?php

defined('TYPO3_MODE') or die();

$GLOBALS['TCA']['pages']['columns']['content'] = [
    'config' => [
        'type' => 'inline',
        'foreign_table' => 'tt_content',
        'foreign_field' => 'pid',
        'grid_area_field' => 'colPos',
        'appearance' => [
            'collapse' => 0,
            'useSortable' => true,
            'levelLinksPosition' => 'top',
            'showSynchronizationLink' => 1,
            'showPossibleLocalizationRecords' => 1,
            'showAllLocalizationLink' => 1,
        ]
    ]
];

$GLOBALS['TCA']['pages']['types'][(string)\TYPO3\CMS\Frontend\Page\PageRepository::DOKTYPE_DEFAULT]['showitem'] .= ',content';

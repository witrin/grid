<?php

defined('TYPO3_MODE') or die();

$GLOBALS['TCA']['pages']['columns']['content'] = [
    'config' => [
        'type' => 'inline',
        'foreign_table' => 'tt_content',
        'foreign_field' => 'pid',
        'grid_area_field' => 'colPos'
    ]
];
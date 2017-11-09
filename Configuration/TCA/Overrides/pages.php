<?php

defined('TYPO3_MODE') or die();

$GLOBALS['TCA']['pages']['columns']['content'] = [
    'config' => [
        'type' => 'grid',
        'foreign_table' => 'tt_content',
        'foreign_field' => 'pid',
        'foreign_area_field' => 'colPos'
    ]
];
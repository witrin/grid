<?php
return [
    'ctrl' => [
        'label' => 'title',
        'sortby' => 'sorting',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'editlock' => 'editlock',
        'title' => 'LLL:EXT:grid_example/Resources/Private/Language/Configuration/TCA/tx_grid_example_domain_model_container.xlf:title',
        'delete' => 'deleted',
        'versioningWS' => true,
        'versioning_followPages' => true,
        'origUid' => 't3_origuid',
        'hideAtCopy' => true,
        'prependAtCopy' => 'LLL:EXT:lang/locallang_general.xlf:LGL.prependAtCopy',
        'copyAfterDuplFields' => 'sys_language_uid',
        'useColumnsForDefaultValues' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'languageField' => 'sys_language_uid',
        'typeicon_classes' => [
            'default' => 'mimetypes-open-document-drawing'
        ],
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
            'fe_group' => 'fe_group'
        ],
        'searchFields' => 'header'
    ],
    'interface' => [
        'always_description' => 0,
        'showRecordFieldList' => 'header,starttime,endtime,fe_group'
    ],
    'columns' => [
        'editlock' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_tca.xlf:editlock',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:lang/locallang_core.xlf:labels.enabled'
                    ]
                ]
            ]
        ],
        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:hidden.I.0'
                    ]
                ]
            ]
        ],
        'starttime' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'size' => '13',
                'eval' => 'datetime',
                'default' => 0
            ],
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly'
        ],
        'endtime' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'size' => '13',
                'eval' => 'datetime',
                'default' => 0,
                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038)
                ]
            ],
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly'
        ],
        'fe_group' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.fe_group',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'size' => 5,
                'maxitems' => 20,
                'items' => [
                    [
                        'LLL:EXT:lang/locallang_general.xlf:LGL.hide_at_login',
                        -1
                    ],
                    [
                        'LLL:EXT:lang/locallang_general.xlf:LGL.any_login',
                        -2
                    ],
                    [
                        'LLL:EXT:lang/locallang_general.xlf:LGL.usergroups',
                        '--div--'
                    ]
                ],
                'exclusiveKeys' => '-1,-2',
                'foreign_table' => 'fe_groups',
                'foreign_table_where' => 'ORDER BY fe_groups.title',
                'enableMultiSelectFilterTextfield' => true
            ]
        ],
        'sys_language_uid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => [
                    [
                        'LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages',
                        -1,
                        'flags-multiple'
                    ],
                ],
                'default' => 0,
            ]
        ],
        'l10n_parent' => [
            'exclude' => 1,
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l10n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        '',
                        0
                    ]
                ],
                'foreign_table' => 'tx_grid_example_domain_model_container',
                'foreign_table_where' => 'AND tx_grid_example_domain_model_container.pid=###CURRENT_PID### AND tx_grid_example_domain_model_container.sys_language_uid IN (-1,0)',
                'default' => 0
            ]
        ],
        'title' => [
            'l10n_mode' => 'prefixLangTitle',
            'l10n_cat' => 'text',
            'label' => 'LLL:EXT:grid_example/Resources/Private/Language/Configuration/TCA/tx_grid_example_domain_model_container.xlf:column.title',
            'config' => [
                'type' => 'input',
                'size' => '50',
                'max' => '255'
            ]
        ],
        'content' => [
            'label' => 'LLL:EXT:grid_example/Resources/Private/Language/Configuration/TCA/tx_grid_example_domain_model_container.xlf:column.content',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_grid_example_domain_model_content',
                'foreign_field' => 'parent',
                'foreign_table_field' => 'table_local',
                'foreign_match_fields' => [
                    'field_local' => 'content'
                ]
            ]
        ]
    ],
    'types' => [
        '0' => [
            'showitem' => '
                    --palette--;LLL:EXT:grid_example/Resources/Private/Language/Configuration/TCA/tx_grid_example_domain_model_container.xlf:palette.general;general,
                    --palette--;LLL:EXT:grid_example/Resources/Private/Language/Configuration/TCA/tx_grid_example_domain_model_container.xlf:palette.header;header,
                    --palette--;LLL:EXT:grid_example/Resources/Private/Language/Configuration/TCA/tx_grid_example_domain_model_container.xlf:palette.content;content,
                --div--;LLL:EXT:grid_example/Resources/Private/Language/Configuration/TCA/tx_grid_example_domain_model_container.xlf:tab.access,
                    --palette--;LLL:EXT:grid_example/Resources/Private/Language/Configuration/TCA/tx_grid_example_domain_model_container.xlf:palette.visibility;visibility,
                    --palette--;LLL:EXT:grid_example/Resources/Private/Language/Configuration/TCA/tx_grid_example_domain_model_container.xlf:palette.access;access
            '
        ]
    ],
    'palettes' => [
        'general' => [
            'showitem' => '
                sys_language_uid, l10n_parent
            '
        ],
        'header' => [
            'showitem' => '
                title
            '
        ],
        'content' => [
            'showitem' => '
                content
            '
        ],
        'visibility' => [
            'showitem' => '
                hidden;LLL:EXT:grid_example/Resources/Private/Language/Configuration/TCA/tx_grid_example_domain_model_container.xlf:column.hidden
            '
        ],
        'access' => [
            'showitem' => '
                starttime,endtime,
                --linebreak--,fe_group,
                --linebreak--,editlock
            '
        ]
    ]
];

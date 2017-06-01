<?php
return [
    'ctrl' => [
        'label' => 'header',
        'sortby' => 'sorting',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'editlock' => 'editlock',
        'hideTable' => true,
        'title' => 'LLL:EXT:grid_example/Resources/Private/Language/Configuration/TCA/tx_grid_example_domain_model_content.xlf:title',
        'delete' => 'deleted',
        'type' => 'type',
        'versioningWS' => true,
        'versioning_followPages' => true,
        'origUid' => 't3_origuid',
        'hideAtCopy' => true,
        'prependAtCopy' => 'LLL:EXT:lang/locallang_general.xlf:LGL.prependAtCopy',
        'copyAfterDuplFields' => 'sys_language_uid',
        'useColumnsForDefaultValues' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'translationSource' => 'l10n_source',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'languageField' => 'sys_language_uid',
        'typeicon_column' => 'type',
        'typeicon_classes' => [
            'text' => 'content-text',
            'media' => 'content-textmedia'
        ],
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
            'fe_group' => 'fe_group'
        ],
        'searchFields' => 'header',
        'gridAreaField' => 'grid_area'
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
                'foreign_table' => 'tx_grid_example_domain_model_content',
                'foreign_table_where' => 'AND tx_grid_example_domain_model_content.pid=###CURRENT_PID### AND tx_grid_example_domain_model_content.sys_language_uid IN (-1,0)',
                'default' => 0
            ]
        ],
        'l10n_source' => [
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'type' => [
            'label' => 'LLL:EXT:grid_example/Resources/Private/Language/Configuration/TCA/tx_grid_example_domain_model_content.xlf:column.type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'LLL:EXT:grid_example/Resources/Private/Language/Configuration/TCA/tx_grid_example_domain_model_content.xlf:column.type.standard',
                        '--div--'
                    ],
                    [
                        'LLL:EXT:grid_example/Resources/Private/Language/Configuration/TCA/tx_grid_example_domain_model_content.xlf:column.type.text',
                        'text',
                        'content-text'
                    ],
                    [
                        'LLL:EXT:grid_example/Resources/Private/Language/Configuration/TCA/tx_grid_example_domain_model_content.xlf:column.type.media',
                        'media',
                        'content-textmedia'
                    ]
                ],
                'default' => 'text',
                'authMode' => $GLOBALS['TYPO3_CONF_VARS']['BE']['explicitADmode'],
                'authMode_enforce' => 'strict',
            ]
        ],
        'grid_area' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:grid_example/Resources/Private/Language/Configuration/TCA/tx_grid_example_domain_model_content.xlf:column.grid_area',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:colPos.I.0',
                        '1'
                    ],
                    [
                        'LLL:EXT:lang/locallang_general.xlf:LGL.normal',
                        '0'
                    ],
                    [
                        'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:colPos.I.2',
                        '2'
                    ],
                    [
                        'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:colPos.I.3',
                        '3'
                    ]
                ],
                'default' => 0
            ]
        ],
        'header' => [
            'l10n_mode' => 'prefixLangTitle',
            'l10n_cat' => 'text',
            'label' => 'LLL:EXT:grid_example/Resources/Private/Language/Configuration/TCA/tx_grid_example_domain_model_content.xlf:column.header',
            'config' => [
                'type' => 'input',
                'size' => '50',
                'max' => '255'
            ]
        ],
        'text' => [
            'label' => 'LLL:EXT:grid_example/Resources/Private/Language/Configuration/TCA/tx_grid_example_domain_model_content.xlf:column.text',
            'l10n_mode' => 'prefixLangTitle',
            'l10n_cat' => 'text',
            'config' => [
                'type' => 'text',
                'cols' => '80',
                'rows' => '15',
                'wizards' => [
                    'RTE' => [
                        'notNewRecords' => 1,
                        'RTEonly' => 1,
                        'type' => 'script',
                        'title' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:bodytext.W.RTE',
                        'icon' => 'EXT:backend/Resources/Public/Images/FormFieldWizard/wizard_rte.gif',
                        'module' => [
                            'name' => 'wizard_rte'
                        ]
                    ]
                ]
            ]
        ],
        'media' => [
            'label' => 'LLL:EXT:grid_example/Resources/Private/Language/Configuration/TCA/tx_grid_example_domain_model_content.xlf:column.media',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('media', [
                'appearance' => [
                    'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:media.addFileReference'
                ]
            ])
        ],
        'parent' => [
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'table_local' => [
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'field_local' => [
            'config' => [
                'type' => 'passthrough'
            ]
        ]
    ],
    'types' => [
        '1' => [
            'showitem' => 'type'
        ],
        'text' => [
            'showitem' => '
                    --palette--;LLL:EXT:grid_example/Resources/Private/Language/Configuration/TCA/tx_grid_example_domain_model_content.xlf:palette.general;general,
                    --palette--;LLL:EXT:grid_example/Resources/Private/Language/Configuration/TCA/tx_grid_example_domain_model_content.xlf:palette.header;header,
                    --palette--;LLL:EXT:grid_example/Resources/Private/Language/Configuration/TCA/tx_grid_example_domain_model_content.xlf:palette.content;content,
                --div--;LLL:EXT:grid_example/Resources/Private/Language/Configuration/TCA/tx_grid_example_domain_model_content.xlf:tab.access,
                    --palette--;LLL:EXT:grid_example/Resources/Private/Language/Configuration/TCA/tx_grid_example_domain_model_content.xlf:palette.visibility;visibility,
                    --palette--;LLL:EXT:grid_example/Resources/Private/Language/Configuration/TCA/tx_grid_example_domain_model_content.xlf:palette.access;access
            '
        ],
        'media' => [
            'showitem' => '
                    --palette--;LLL:EXT:grid_example/Resources/Private/Language/Configuration/TCA/tx_grid_example_domain_model_content.xlf:palette.general;general,
                    --palette--;LLL:EXT:grid_example/Resources/Private/Language/Configuration/TCA/tx_grid_example_domain_model_content.xlf:palette.header;header,
                    --palette--;LLL:EXT:grid_example/Resources/Private/Language/Configuration/TCA/tx_grid_example_domain_model_content.xlf:palette.content;content,
                --div--;LLL:EXT:grid_example/Resources/Private/Language/Configuration/TCA/tx_grid_example_domain_model_content.xlf:tab.resources,
                    --palette--;LLL:EXT:grid_example/Resources/Private/Language/Configuration/TCA/tx_grid_example_domain_model_content.xlf:palette.files;files,
                --div--;LLL:EXT:grid_example/Resources/Private/Language/Configuration/TCA/tx_grid_example_domain_model_content.xlf:tab.access,
                    --palette--;LLL:EXT:grid_example/Resources/Private/Language/Configuration/TCA/tx_grid_example_domain_model_content.xlf:palette.visibility;visibility,
                    --palette--;LLL:EXT:grid_example/Resources/Private/Language/Configuration/TCA/tx_grid_example_domain_model_content.xlf:palette.access;access
            '
        ]
    ],
    'palettes' => [
        'general' => [
            'showitem' => '
                type, grid_area, sys_language_uid,
                --linebreak--, l10n_parent
            '
        ],
        'header' => [
            'showitem' => '
                header
            '
        ],
        'content' => [
            'showitem' => '
                text
            '
        ],
        'files' => [
            'showitem' => '
                media
            '
        ],
        'visibility' => [
            'showitem' => '
                hidden;LLL:EXT:grid_example/Resources/Private/Language/Configuration/TCA/tx_grid_example_domain_model_content.xlf:column.hidden
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

<?php
defined('TYPO3_MODE') or die();

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1466746089] = [
    'nodeName' => 'layoutContainer',
    'priority' => 40,
    'class' => \TYPO3\CMS\Grid\Form\Node\LayoutContainer::class
];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1496091995] = [
    'nodeName' => 'localizationContainer',
    'priority' => 40,
    'class' => \TYPO3\CMS\Grid\Form\Node\LayoutOverlayContainer::class
];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1466746106] = [
    'nodeName' => 'contentPreview',
    'priority' => 40,
    'class' => \TYPO3\CMS\Grid\Form\Node\ContentPreview::class
];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1466746109] = [
    'nodeName' => 'contentPositionContainer',
    'priority' => 40,
    'class' => \TYPO3\CMS\Grid\Form\Node\ContentPositionContainer::class
];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1466746112] = [
    'nodeName' => 'contentPresetSidebarContainer',
    'priority' => 40,
    'class' => \TYPO3\CMS\Grid\Form\Node\ContentPresetSidebarContainer::class
];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1466746113] = [
    'nodeName' => 'contentPresetTabContainer',
    'priority' => 40,
    'class' => \TYPO3\CMS\Grid\Form\Node\ContentPresetTabContainer::class
];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1466746108] = [
    'nodeName' => 'contentPreset',
    'priority' => 40,
    'class' => \TYPO3\CMS\Grid\Form\Node\ContentPreset::class
];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeResolver'][1466746108] = [
    'nodeName' => 'contentPreview',
    'priority' => 40,
    'class' => \TYPO3\CMS\Grid\Form\Node\PageContentPreview::class
];


$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['contentContainer'] = array_merge(
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'],
    [
        \TYPO3\CMS\Grid\Form\Data\LanguageOverlayProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\DatabaseEditRow::class,
                \TYPO3\CMS\Backend\Form\FormDataProvider\DatabaseSystemLanguageRows::class,
                \TYPO3\CMS\Backend\Form\FormDataProvider\InitializeProcessedTca::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\ItemDataProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\LanguageOverlayProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\LanguageProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\ItemDataProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\ItemConfigProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInline::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\ItemTcaProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\ItemConfigProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\ItemDefaultValuesProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\ItemConfigProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\ItemAppendActionProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\InitializeProcessedTca::class,
                \TYPO3\CMS\Backend\Form\FormDataProvider\PageTsConfigMerged::class,
                \TYPO3\CMS\Backend\Form\FormDataProvider\DatabaseEffectivePid::class,
                \TYPO3\CMS\Grid\Form\Data\LanguageProvider::class,
                \TYPO3\CMS\Grid\Form\Data\TemplateDefinitionProvider::class,
                \TYPO3\CMS\Grid\Form\Data\ItemConfigProvider::class,
                \TYPO3\CMS\Grid\Form\Data\ItemTcaProvider::class,
                \TYPO3\CMS\Grid\Form\Data\ItemDefaultValuesProvider::class,
                \TYPO3\CMS\Grid\Form\Data\ItemDataProvider::class,
                \TYPO3\CMS\Grid\Form\Data\AreaItemsProvider::class,
                \TYPO3\CMS\Grid\Form\Data\Layout\LocalizationModeProvider::class,
                \TYPO3\CMS\Grid\Form\Data\Layout\LocalizationStatusProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\ItemEditActionProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\ItemDataProvider::class
            ],
            'before' => [
                \TYPO3\CMS\Grid\Form\Data\Layout\ItemVisibilityActionProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\ItemPreviewTemplateProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\ItemDataProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\ItemDeleteActionProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\ItemDataProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\ItemVisibilityActionProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\ItemDataProvider::class
            ],
            'before' => [
                \TYPO3\CMS\Grid\Form\Data\Layout\ItemDeleteActionProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\ItemVisibilityProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\ItemDataProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\LanguageProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\ItemDataProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\TemplateDefinitionProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\ItemConfigProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\LocalizeActionProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\InitializeProcessedTca::class,
                \TYPO3\CMS\Backend\Form\FormDataProvider\PageTsConfigMerged::class,
                \TYPO3\CMS\Backend\Form\FormDataProvider\DatabaseEffectivePid::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\TemplateDimensionsProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\PageLayout\TemplateDefinitionProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\AreaCollisionProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\TemplateDimensionsProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\AreaSortingProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\TemplateDefinitionProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\AreaAccessProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\TemplateDefinitionProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\AreaItemsProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\TemplateDefinitionProvider::class,
                \TYPO3\CMS\Grid\Form\Data\ItemDataProvider::class,
                \TYPO3\CMS\Grid\Form\Data\ItemTcaProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\UnusedItemsProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\AreaItemsProvider::class,
                \TYPO3\CMS\Grid\Form\Data\TemplateDimensionsProvider::class,
                \TYPO3\CMS\Grid\Form\Data\Layout\ItemAppendActionProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\AreaInsertActionProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\InitializeProcessedTca::class,
                \TYPO3\CMS\Backend\Form\FormDataProvider\PageTsConfigMerged::class,
                \TYPO3\CMS\Backend\Form\FormDataProvider\DatabaseEffectivePid::class,
                \TYPO3\CMS\Grid\Form\Data\LanguageProvider::class,
                \TYPO3\CMS\Grid\Form\Data\TemplateDefinitionProvider::class,
                \TYPO3\CMS\Grid\Form\Data\ItemConfigProvider::class,
                \TYPO3\CMS\Grid\Form\Data\ItemTcaProvider::class,
                \TYPO3\CMS\Grid\Form\Data\ItemDataProvider::class,
                \TYPO3\CMS\Grid\Form\Data\ItemDefaultValuesProvider::class,
                \TYPO3\CMS\Grid\Form\Data\Layout\LocalizationModeProvider::class,
                \TYPO3\CMS\Grid\Form\Data\Layout\LocalizationStatusProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\AreaEditActionProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\DatabaseParentPageRow::class,
                \TYPO3\CMS\Backend\Form\FormDataProvider\TcaRecordTitle::class,
                \TYPO3\CMS\Grid\Form\Data\LanguageProvider::class,
                \TYPO3\CMS\Grid\Form\Data\AreaItemsProvider::class,
                \TYPO3\CMS\Grid\Form\Data\ItemConfigProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\AreaLocalizeActionProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\LanguageProvider::class,
                \TYPO3\CMS\Grid\Form\Data\LanguageOverlayProvider::class,
                \TYPO3\CMS\Grid\Form\Data\Layout\LocalizationStrategyProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\ItemPresetsProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInline::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\LocalizationStatusProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\AreaItemsProvider::class,
                \TYPO3\CMS\Grid\Form\Data\Layout\LocalizationModeProvider::class,
                \TYPO3\CMS\Grid\Form\Data\LanguageProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\LocalizationModeProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInline::class
            ],
            'before' => [
                \TYPO3\CMS\Grid\Form\Data\ItemDataProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\ViewActionProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\DatabaseEditRow::class
            ],
            'before' => [
                \TYPO3\CMS\Grid\Form\Data\Layout\EditActionProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\EditActionProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\DatabaseEditRow::class,
                \TYPO3\CMS\Grid\Form\Data\LanguageProvider::class
            ]
        ]
    ]
);

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['pageLayout'] = array_merge(
    array_diff_key(
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['contentContainer'],
        array_flip([
            \TYPO3\CMS\Grid\Form\Data\LanguageOverlayProvider::class,
            \TYPO3\CMS\Grid\Form\Data\TemplateDefinitionProvider::class,
            \TYPO3\CMS\Grid\Form\Data\UnusedItemsProvider::class,
            \TYPO3\CMS\Grid\Form\Data\Layout\ItemAppendActionProvider::class,
            \TYPO3\CMS\Grid\Form\Data\Layout\ItemPreviewTemplateProvider::class,
            \TYPO3\CMS\Grid\Form\Data\Layout\AreaLocalizeActionProvider::class,
            \TYPO3\CMS\Grid\Form\Data\Layout\AreaInsertActionProvider::class,
            \TYPO3\CMS\Grid\Form\Data\Layout\LocalizationStatusProvider::class,
            \TYPO3\CMS\Grid\Form\Data\Layout\LocalizeActionProvider::class
        ])
    ),
    [
        \TYPO3\CMS\Grid\Form\Data\PageLayout\LocalizeActionProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\InitializeProcessedTca::class,
                \TYPO3\CMS\Backend\Form\FormDataProvider\PageTsConfigMerged::class,
                \TYPO3\CMS\Backend\Form\FormDataProvider\DatabaseEffectivePid::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\PageLayout\AreaInsertActionProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\InitializeProcessedTca::class,
                \TYPO3\CMS\Backend\Form\FormDataProvider\PageTsConfigMerged::class,
                \TYPO3\CMS\Backend\Form\FormDataProvider\DatabaseEffectivePid::class,
                \TYPO3\CMS\Grid\Form\Data\ItemConfigProvider::class,
                \TYPO3\CMS\Grid\Form\Data\ItemTcaProvider::class,
                \TYPO3\CMS\Grid\Form\Data\ItemDataProvider::class,
                \TYPO3\CMS\Grid\Form\Data\ItemDefaultValuesProvider::class,
                \TYPO3\CMS\Grid\Form\Data\LanguageProvider::class,
                \TYPO3\CMS\Grid\Form\Data\Layout\LocalizationModeProvider::class,
                \TYPO3\CMS\Grid\Form\Data\PageLayout\LocalizationStatusProvider::class,
                \TYPO3\CMS\Grid\Form\Data\PageLayout\TemplateDefinitionProvider::class,
                \TYPO3\CMS\Grid\Form\Data\PageLayout\LanguageOverlayProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\PageLayout\LocalizationStatusProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\AreaItemsProvider::class,
                \TYPO3\CMS\Grid\Form\Data\LanguageProvider::class,
                \TYPO3\CMS\Grid\Form\Data\Layout\AreaInsertActionProvider::class,
                \TYPO3\CMS\Grid\Form\Data\Layout\ItemAppendActionProvider::class,
                \TYPO3\CMS\Grid\Form\Data\PageLayout\LocalizationModeProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\PageLayout\LanguageOverlayProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\ItemDataProvider::class,
                \TYPO3\CMS\Grid\Form\Data\LanguageProvider::class
            ],
            'before' => [
                \TYPO3\CMS\Grid\Form\Data\AreaItemsProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\PageLayout\TemplateDefinitionProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInline::class
            ],
            'before' => [
                \TYPO3\CMS\Grid\Form\Data\ItemDataProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\PageLayout\ItemAppendActionProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\InitializeProcessedTca::class,
                \TYPO3\CMS\Backend\Form\FormDataProvider\PageTsConfigMerged::class,
                \TYPO3\CMS\Backend\Form\FormDataProvider\DatabaseEffectivePid::class,
                \TYPO3\CMS\Grid\Form\Data\TemplateDefinitionProvider::class,
                \TYPO3\CMS\Grid\Form\Data\ItemConfigProvider::class,
                \TYPO3\CMS\Grid\Form\Data\ItemTcaProvider::class,
                \TYPO3\CMS\Grid\Form\Data\ItemDefaultValuesProvider::class,
                \TYPO3\CMS\Grid\Form\Data\ItemDataProvider::class,
                \TYPO3\CMS\Grid\Form\Data\AreaItemsProvider::class,
                \TYPO3\CMS\Grid\Form\Data\LanguageProvider::class,
                \TYPO3\CMS\Grid\Form\Data\Layout\LocalizationModeProvider::class,
                \TYPO3\CMS\Grid\Form\Data\PageLayout\LocalizationStatusProvider::class,
                \TYPO3\CMS\Grid\Form\Data\PageLayout\LanguageOverlayProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\PageLayout\ItemPreviewTemplateProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\ItemDataProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\TemplateDimensionsProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\PageLayout\TemplateDefinitionProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\AreaSortingProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\PageLayout\TemplateDefinitionProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\AreaAccessProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\PageLayout\TemplateDefinitionProvider::class
            ],
            'before' => [
                \TYPO3\CMS\Grid\Form\Data\ItemDataProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\PageLayout\UnusedItemsProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\AreaItemsProvider::class,
                \TYPO3\CMS\Grid\Form\Data\TemplateDimensionsProvider::class,
                \TYPO3\CMS\Grid\Form\Data\PageLayout\LanguageOverlayProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\PageLayout\ItemPresetsProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInline::class
            ],
            'before' => [
                \TYPO3\CMS\Grid\Form\Data\Layout\ItemPresetsProvider::class,
                \TYPO3\CMS\Grid\Form\Data\ItemDataProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\PageLayout\LocalizationModeProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInline::class
            ],
            'before' => [
                \TYPO3\CMS\Grid\Form\Data\ItemDataProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\PageLayout\AreaLocalizeActionProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\LanguageProvider::class,
                \TYPO3\CMS\Grid\Form\Data\LanguageOverlayProvider::class,
                \TYPO3\CMS\Grid\Form\Data\Layout\LocalizationStrategyProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\PageLayout\PageInfoProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\DatabaseEditRow::class,
                \TYPO3\CMS\Backend\Form\FormDataProvider\TcaSelectItems::class
            ]
        ]
    ]
);

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['contentCreation'] = array_merge(
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'],
    [
        \TYPO3\CMS\Grid\Form\Data\ItemConfigProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInline::class
            ],
            'before' => [
                \TYPO3\CMS\Grid\Form\Data\ItemDataProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\ItemTcaProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\ItemConfigProvider::class
            ],
            'before' => [
                \TYPO3\CMS\Grid\Form\Data\ItemDataProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\ItemDefaultValuesProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\ItemTcaProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\ItemPresetsProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\PageTsConfig::class,
                \TYPO3\CMS\Grid\Form\Data\ItemTcaProvider::class
            ]
        ]
    ]
);

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['pageContentCreation'] = array_merge(
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['contentCreation'],
    [
        \TYPO3\CMS\Grid\Form\Data\PageLayout\ItemPresetsProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInline::class
            ],
            'before' => [
                \TYPO3\CMS\Grid\Form\Data\Layout\ItemPresetsProvider::class,
                \TYPO3\CMS\Grid\Form\Data\ItemDataProvider::class
            ]
        ]
    ]
);

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
    'class' => \TYPO3\CMS\Grid\Form\Node\LocalizationContainer::class
];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1466746106] = [
    'nodeName' => 'contentPreview',
    'priority' => 40,
    'class' => \TYPO3\CMS\Grid\Form\Node\ContentPreview::class
];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1466746109] = [
    'nodeName' => 'backendLayoutPositionContainer',
    'priority' => 40,
    'class' => \TYPO3\CMS\Grid\Form\Container\BackendLayout\PositionContainer::class
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
    // @todo the group `tcaDatabaseRecord` can not reduced only by using the TCA because of the provider `TcaInline`
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'],
    [
        \TYPO3\CMS\Grid\Form\Data\LanguageUidProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\ItemRecordsProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\ItemsConfigProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInline::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\ItemsTcaProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\ItemsConfigProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\ItemsDefaultValuesProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\ItemsConfigProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\ItemRecordsProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInline::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\AppendItemActionProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\ItemRecordsProvider::class,
                \TYPO3\CMS\Grid\Form\Data\TemplateAreasItemsProvider::class,
                \TYPO3\CMS\Grid\Form\Data\ItemsDefaultValuesProvider::class,
                \TYPO3\CMS\Grid\Form\Data\LanguageUidProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\EditItemActionProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\ItemRecordsProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\ItemPreviewTemplateProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\ItemRecordsProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\DeleteItemActionProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\ItemRecordsProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\PrependItemActionProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\ItemRecordsProvider::class,
                \TYPO3\CMS\Grid\Form\Data\TemplateAreasItemsProvider::class,
                \TYPO3\CMS\Grid\Form\Data\ItemsDefaultValuesProvider::class,
                \TYPO3\CMS\Grid\Form\Data\LanguageUidProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\ToggleItemVisibilityActionProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\ItemRecordsProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\ItemVisibilityProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\ItemRecordsProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\LanguageUidProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\ItemRecordsProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\TemplateProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\ItemsConfigProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\TemplateDimensionsProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\PageLayout\TemplateProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\TemplateAreasCollisionsProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\TemplateDimensionsProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\TemplateAreasSortingProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\TemplateProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\TemplateAreasOverlaysProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\TemplateProvider::class,
                \TYPO3\CMS\Grid\Form\Data\LanguageUidProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\TemplateAreasAccessProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\TemplateProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\TemplateAreasItemsProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\TemplateProvider::class,
                \TYPO3\CMS\Grid\Form\Data\ItemRecordsProvider::class,
                \TYPO3\CMS\Grid\Form\Data\ItemsTcaProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\TemplateAreasInsertActionProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\Layout\TemplateAreasOverlaysProvider::class,
                \TYPO3\CMS\Grid\Form\Data\LanguageUidProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\TemplateAreasLocalizeActionProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\Layout\TemplateAreasOverlaysProvider::class,
                \TYPO3\CMS\Grid\Form\Data\LanguageUidProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\ItemPresetsProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInline::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\LocalizationStrategyProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\ItemRecordsProvider::class,
                \TYPO3\CMS\Grid\Form\Data\LanguageUidProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\LocalizationStatusProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\TemplateAreasItemsProvider::class,
                \TYPO3\CMS\Grid\Form\Data\LanguageUidProvider::class
            ]
        ]
    ]
);

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['pageLayout'] = array_merge(
    // @todo the group `tcaDatabaseRecord` can not reduced only by using the TCA because of the provider `TcaInline`
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'],
    [
        \TYPO3\CMS\Grid\Form\Data\ItemsConfigProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInline::class
            ],
            'before' => [
                \TYPO3\CMS\Grid\Form\Data\ItemRecordsProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\ItemsTcaProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\ItemsConfigProvider::class
            ],
            'before' => [
                \TYPO3\CMS\Grid\Form\Data\ItemRecordsProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\ItemsDefaultValuesProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\ItemsConfigProvider::class
            ],
            'before' => [
                \TYPO3\CMS\Grid\Form\Data\ItemRecordsProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\ItemRecordsProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInline::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\PageLayout\TemplateProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInline::class
            ],
            'before' => [
                \TYPO3\CMS\Grid\Form\Data\ItemRecordsProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\PageLayout\AppendItemActionProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\ItemRecordsProvider::class,
                \TYPO3\CMS\Grid\Form\Data\TemplateAreasItemsProvider::class,
                \TYPO3\CMS\Grid\Form\Data\ItemsDefaultValuesProvider::class,
                \TYPO3\CMS\Grid\Form\Data\LanguageUidProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\EditItemActionProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\ItemRecordsProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\PageLayout\ItemPreviewTemplateProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\ItemRecordsProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\DeleteItemActionProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\ItemRecordsProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\PrependItemActionProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\ItemRecordsProvider::class,
                \TYPO3\CMS\Grid\Form\Data\TemplateAreasItemsProvider::class,
                \TYPO3\CMS\Grid\Form\Data\ItemsDefaultValuesProvider::class,
                \TYPO3\CMS\Grid\Form\Data\LanguageUidProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\ToggleItemVisibilityActionProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\ItemRecordsProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\ItemVisibilityProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\ItemRecordsProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\LanguageUidProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\ItemRecordsProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\TemplateDimensionsProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\PageLayout\TemplateProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\TemplateAreasCollisionsProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\TemplateDimensionsProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\TemplateAreasSortingProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\PageLayout\TemplateProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\TemplateAreasOverlaysProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\PageLayout\TemplateProvider::class,
                \TYPO3\CMS\Grid\Form\Data\LanguageUidProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\TemplateAreasAccessProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\PageLayout\TemplateProvider::class
            ],
            'before' => [
                \TYPO3\CMS\Grid\Form\Data\ItemRecordsProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\TemplateAreasItemsProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\TemplateProvider::class,
                \TYPO3\CMS\Grid\Form\Data\ItemRecordsProvider::class,
                \TYPO3\CMS\Grid\Form\Data\ItemsTcaProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\LocalizationStrategyProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\ItemRecordsProvider::class,
                \TYPO3\CMS\Grid\Form\Data\LanguageUidProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\LocalizationStatusProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\TemplateAreasItemsProvider::class,
                \TYPO3\CMS\Grid\Form\Data\LanguageUidProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\TemplateAreasLocalizeActionProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\Layout\LocalizationStatusProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\TemplateAreasInsertActionProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\Layout\TemplateAreasOverlaysProvider::class,
                \TYPO3\CMS\Grid\Form\Data\LanguageUidProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\TemplateAreasLocalizeActionProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\Layout\TemplateAreasOverlaysProvider::class,
                \TYPO3\CMS\Grid\Form\Data\LanguageUidProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\ItemPresetsProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\PageTsConfig::class,
                \TYPO3\CMS\Grid\Form\Data\ItemsTcaProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\PageLayout\ItemPresetsProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInline::class
            ],
            'before' => [
                \TYPO3\CMS\Grid\Form\Data\Layout\ItemPresetsProvider::class,
                \TYPO3\CMS\Grid\Form\Data\ItemRecordsProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\PageLayout\LocalizationModeProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInline::class
            ],
            'before' => [
                \TYPO3\CMS\Grid\Form\Data\ItemRecordsProvider::class
            ]
        ]
    ]
);

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['contentCreation'] = array_merge(
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'],
    [
        \TYPO3\CMS\Grid\Form\Data\ItemRecordsProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInline::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\ItemsConfigProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInline::class
            ],
            'before' => [
                \TYPO3\CMS\Grid\Form\Data\ItemRecordsProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\ItemsTcaProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\ItemsConfigProvider::class
            ],
            'before' => [
                \TYPO3\CMS\Grid\Form\Data\ItemRecordsProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\ItemsDefaultValuesProvider::class => [
            'depends' => [
                \TYPO3\CMS\Grid\Form\Data\ItemsTcaProvider::class
            ]
        ],
        \TYPO3\CMS\Grid\Form\Data\Layout\ItemPresetsProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\PageTsConfig::class,
                \TYPO3\CMS\Grid\Form\Data\ItemsTcaProvider::class
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
                \TYPO3\CMS\Grid\Form\Data\ItemRecordsProvider::class
            ]
        ]
    ]
);

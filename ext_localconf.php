<?php
defined('TYPO3_MODE') or die();

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1466746089] = [
    'nodeName' => 'layoutContainer',
    'priority' => 40,
    'class' => \TYPO3\CMS\Wireframe\Form\Node\LayoutContainer::class
];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1496091995] = [
    'nodeName' => 'localizationContainer',
    'priority' => 40,
    'class' => \TYPO3\CMS\Wireframe\Form\Node\LocalizationContainer::class
];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1466746106] = [
    'nodeName' => 'contentPreview',
    'priority' => 40,
    'class' => \TYPO3\CMS\Wireframe\Form\Node\ContentPreview::class
];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1466746109] = [
    'nodeName' => 'backendLayoutPositionContainer',
    'priority' => 40,
    'class' => \TYPO3\CMS\Wireframe\Form\Container\BackendLayout\PositionContainer::class
];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1466746112] = [
    'nodeName' => 'contentPresetSidebarContainer',
    'priority' => 40,
    'class' => \TYPO3\CMS\Wireframe\Form\Node\ContentPresetSidebarContainer::class
];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1466746113] = [
    'nodeName' => 'contentPresetTabContainer',
    'priority' => 40,
    'class' => \TYPO3\CMS\Wireframe\Form\Node\ContentPresetTabContainer::class
];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1466746108] = [
    'nodeName' => 'contentPreset',
    'priority' => 40,
    'class' => \TYPO3\CMS\Wireframe\Form\Node\ContentPreset::class
];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeResolver'][1466746108] = [
    'nodeName' => 'contentPreview',
    'priority' => 40,
    'class' => \TYPO3\CMS\Wireframe\Form\Node\PageContentPreview::class
];


$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['contentContainer'] = array_merge(
    // @todo the group `tcaDatabaseRecord` can not reduced only by using the TCA because of the provider `TcaInline`
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'],
    [
        \TYPO3\CMS\Wireframe\Form\Data\Record\LanguageUidProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInline::class
            ]
        ],
        \TYPO3\CMS\Wireframe\Form\Data\GridContainer\ItemsConfigProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInline::class
            ]
        ],
        \TYPO3\CMS\Wireframe\Form\Data\GridContainer\ItemsTcaProvider::class => [
            'depends' => [
                \TYPO3\CMS\Wireframe\Form\Data\GridContainer\ItemsConfigProvider::class
            ]
        ],
        \TYPO3\CMS\Wireframe\Form\Data\GridContainer\ItemsDefaultValuesProvider::class => [
            'depends' => [
                \TYPO3\CMS\Wireframe\Form\Data\GridContainer\ItemsConfigProvider::class
            ]
        ],
        \TYPO3\CMS\Wireframe\Form\Data\GridContainer\ItemsProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInline::class
            ]
        ],
        \TYPO3\CMS\Wireframe\Form\Data\GridContainer\TemplateProvider::class => [
            'depends' => [
                \TYPO3\CMS\Wireframe\Form\Data\GridContainer\ItemsConfigProvider::class
            ]
        ],
        \TYPO3\CMS\Wireframe\Form\Data\GridContainer\TemplateDimensionsProvider::class => [
            'depends' => [
                \TYPO3\CMS\Wireframe\Form\Data\PageLayout\TemplateProvider::class
            ]
        ],
        \TYPO3\CMS\Wireframe\Form\Data\GridContainer\TemplateAreasCollisionsProvider::class => [
            'depends' => [
                \TYPO3\CMS\Wireframe\Form\Data\GridContainer\TemplateDimensionsProvider::class
            ]
        ],
        \TYPO3\CMS\Wireframe\Form\Data\GridContainer\TemplateAreasSortingProvider::class => [
            'depends' => [
                \TYPO3\CMS\Wireframe\Form\Data\GridContainer\TemplateProvider::class
            ]
        ],
        \TYPO3\CMS\Wireframe\Form\Data\LayoutContainer\TemplateAreasOverlaysProvider::class => [
            'depends' => [
                \TYPO3\CMS\Wireframe\Form\Data\GridContainer\TemplateProvider::class
            ]
        ],
        \TYPO3\CMS\Wireframe\Form\Data\GridContainer\TemplateAreasAccessProvider::class => [
            'depends' => [
                \TYPO3\CMS\Wireframe\Form\Data\GridContainer\TemplateProvider::class
            ]
        ],
        \TYPO3\CMS\Wireframe\Form\Data\GridContainer\TemplateAreasItemsProvider::class => [
            'depends' => [
                \TYPO3\CMS\Wireframe\Form\Data\GridContainer\TemplateProvider::class,
                \TYPO3\CMS\Wireframe\Form\Data\GridContainer\ItemsProvider::class,
                \TYPO3\CMS\Wireframe\Form\Data\GridContainer\ItemsTcaProvider::class
            ]
        ],
        \TYPO3\CMS\Wireframe\Form\Data\LayoutContainer\TemplateAreasInsertActionProvider::class => [
            'depends' => [
                \TYPO3\CMS\Wireframe\Form\Data\LayoutContainer\TemplateAreasOverlaysProvider::class
            ]
        ],
        \TYPO3\CMS\Wireframe\Form\Data\LayoutContainer\TemplateAreasLocalizeActionProvider::class => [
            'depends' => [
                \TYPO3\CMS\Wireframe\Form\Data\LayoutContainer\TemplateAreasOverlaysProvider::class
            ]
        ],
        \TYPO3\CMS\Wireframe\Form\Data\LayoutContainer\ItemPresetsProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInline::class
            ]
        ],
        \TYPO3\CMS\Wireframe\Form\Data\LayoutContainer\LocalizationStrategyProvider::class => [
            'depends' => [
                \TYPO3\CMS\Wireframe\Form\Data\GridContainer\ItemsProvider::class
            ]
        ],
        \TYPO3\CMS\Wireframe\Form\Data\LayoutContainer\LocalizationStatusProvider::class => [
            'depends' => [
                \TYPO3\CMS\Wireframe\Form\Data\GridContainer\TemplateAreasItemsProvider::class
            ]
        ]
    ]
);

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['contentElement'] = array_merge(
    [
        \TYPO3\CMS\Wireframe\Form\Data\GridItem\AreaUidProvider::class => [],
        \TYPO3\CMS\Wireframe\Form\Data\ContentElement\EditActionProvider::class => [],
        \TYPO3\CMS\Wireframe\Form\Data\ContentElement\PreviewTemplateProvider::class => [],
        \TYPO3\CMS\Wireframe\Form\Data\ContentElement\DeleteActionProvider::class => [],
        \TYPO3\CMS\Wireframe\Form\Data\ContentElement\PrependActionProvider::class => [],
        \TYPO3\CMS\Wireframe\Form\Data\ContentElement\ToggleActionProvider::class => [],
        \TYPO3\CMS\Wireframe\Form\Data\ContentElement\VisibilityProvider::class => [],
        \TYPO3\CMS\Wireframe\Form\Data\Record\LanguageUidProvider::class => [],
    ]
);

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['pageLayout'] = array_merge(
    // @todo the group `tcaDatabaseRecord` can not reduced only by using the TCA because of the provider `TcaInline`
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'],
    [
        \TYPO3\CMS\Wireframe\Form\Data\GridContainer\ItemsConfigProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInline::class
            ],
            'before' => [
                \TYPO3\CMS\Wireframe\Form\Data\GridContainer\ItemsProvider::class
            ]
        ],
        \TYPO3\CMS\Wireframe\Form\Data\GridContainer\ItemsTcaProvider::class => [
            'depends' => [
                \TYPO3\CMS\Wireframe\Form\Data\GridContainer\ItemsConfigProvider::class
            ],
            'before' => [
                \TYPO3\CMS\Wireframe\Form\Data\GridContainer\ItemsProvider::class
            ]
        ],
        \TYPO3\CMS\Wireframe\Form\Data\GridContainer\ItemsDefaultValuesProvider::class => [
            'depends' => [
                \TYPO3\CMS\Wireframe\Form\Data\GridContainer\ItemsConfigProvider::class
            ],
            'before' => [
                \TYPO3\CMS\Wireframe\Form\Data\GridContainer\ItemsProvider::class
            ]
        ],
        \TYPO3\CMS\Wireframe\Form\Data\GridContainer\ItemsProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInline::class
            ]
        ],
        \TYPO3\CMS\Wireframe\Form\Data\PageLayout\TemplateProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInline::class
            ],
            'before' => [
                \TYPO3\CMS\Wireframe\Form\Data\GridContainer\ItemsProvider::class
            ]
        ],
        \TYPO3\CMS\Wireframe\Form\Data\GridContainer\TemplateDimensionsProvider::class => [
            'depends' => [
                \TYPO3\CMS\Wireframe\Form\Data\PageLayout\TemplateProvider::class
            ]
        ],
        \TYPO3\CMS\Wireframe\Form\Data\GridContainer\TemplateAreasCollisionsProvider::class => [
            'depends' => [
                \TYPO3\CMS\Wireframe\Form\Data\GridContainer\TemplateDimensionsProvider::class
            ]
        ],
        \TYPO3\CMS\Wireframe\Form\Data\GridContainer\TemplateAreasSortingProvider::class => [
            'depends' => [
                \TYPO3\CMS\Wireframe\Form\Data\PageLayout\TemplateProvider::class
            ]
        ],
        \TYPO3\CMS\Wireframe\Form\Data\LayoutContainer\TemplateAreasOverlaysProvider::class => [
            'depends' => [
                \TYPO3\CMS\Wireframe\Form\Data\PageLayout\TemplateProvider::class
            ]
        ],
        \TYPO3\CMS\Wireframe\Form\Data\GridContainer\TemplateAreasAccessProvider::class => [
            'depends' => [
                \TYPO3\CMS\Wireframe\Form\Data\PageLayout\TemplateProvider::class
            ],
            'before' => [
                \TYPO3\CMS\Wireframe\Form\Data\GridContainer\ItemsProvider::class
            ]
        ],
        \TYPO3\CMS\Wireframe\Form\Data\GridContainer\TemplateAreasItemsProvider::class => [
            'depends' => [
                \TYPO3\CMS\Wireframe\Form\Data\GridContainer\TemplateProvider::class,
                \TYPO3\CMS\Wireframe\Form\Data\GridContainer\ItemsProvider::class,
                \TYPO3\CMS\Wireframe\Form\Data\GridContainer\ItemsTcaProvider::class
            ]
        ],
        \TYPO3\CMS\Wireframe\Form\Data\LayoutContainer\LocalizationStrategyProvider::class => [
            'depends' => [
                \TYPO3\CMS\Wireframe\Form\Data\GridContainer\ItemsProvider::class
            ]
        ],
        \TYPO3\CMS\Wireframe\Form\Data\LayoutContainer\LocalizationStatusProvider::class => [
            'depends' => [
                \TYPO3\CMS\Wireframe\Form\Data\GridContainer\TemplateAreasItemsProvider::class
            ]
        ],
        \TYPO3\CMS\Wireframe\Form\Data\LayoutContainer\TemplateAreasLocalizeActionProvider::class => [
            'depends' => [
                \TYPO3\CMS\Wireframe\Form\Data\LayoutContainer\LocalizationStatusProvider::class
            ]
        ],
        \TYPO3\CMS\Wireframe\Form\Data\LayoutContainer\TemplateAreasInsertActionProvider::class => [
            'depends' => [
                \TYPO3\CMS\Wireframe\Form\Data\LayoutContainer\TemplateAreasOverlaysProvider::class
            ]
        ],
        \TYPO3\CMS\Wireframe\Form\Data\LayoutContainer\TemplateAreasLocalizeActionProvider::class => [
            'depends' => [
                \TYPO3\CMS\Wireframe\Form\Data\LayoutContainer\TemplateAreasOverlaysProvider::class
            ]
        ],
        \TYPO3\CMS\Wireframe\Form\Data\LayoutContainer\ItemPresetsProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\PageTsConfig::class,
                \TYPO3\CMS\Wireframe\Form\Data\GridContainer\ItemsTcaProvider::class
            ]
        ],
        \TYPO3\CMS\Wireframe\Form\Data\PageLayout\ItemPresetsProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInline::class
            ],
            'before' => [
                \TYPO3\CMS\Wireframe\Form\Data\LayoutContainer\ItemPresetsProvider::class,
                \TYPO3\CMS\Wireframe\Form\Data\GridContainer\ItemsProvider::class
            ]
        ],
        \TYPO3\CMS\Wireframe\Form\Data\PageLayout\LocalizationModeProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInline::class
            ],
            'before' => [
                \TYPO3\CMS\Wireframe\Form\Data\GridContainer\ItemsProvider::class
            ]
        ]
    ]
);

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['pageContent'] = array_merge(
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['contentElement'],
    [
        \TYPO3\CMS\Wireframe\Form\Data\PageContent\AppendActionProvider::class => [],
        \TYPO3\CMS\Wireframe\Form\Data\PageContent\PreviewTemplateProvider::class => []
    ]
);

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['contentCreation'] = array_merge(
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'],
    [
        \TYPO3\CMS\Wireframe\Form\Data\GridContainer\ItemsProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInline::class
            ]
        ],
        \TYPO3\CMS\Wireframe\Form\Data\GridContainer\ItemsConfigProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInline::class
            ],
            'before' => [
                \TYPO3\CMS\Wireframe\Form\Data\GridContainer\ItemsProvider::class
            ]
        ],
        \TYPO3\CMS\Wireframe\Form\Data\GridContainer\ItemsTcaProvider::class => [
            'depends' => [
                \TYPO3\CMS\Wireframe\Form\Data\GridContainer\ItemsConfigProvider::class
            ],
            'before' => [
                \TYPO3\CMS\Wireframe\Form\Data\GridContainer\ItemsProvider::class
            ]
        ],
        \TYPO3\CMS\Wireframe\Form\Data\GridContainer\ItemsDefaultValuesProvider::class => [
            'depends' => [
                \TYPO3\CMS\Wireframe\Form\Data\GridContainer\ItemsTcaProvider::class
            ]
        ],
        \TYPO3\CMS\Wireframe\Form\Data\LayoutContainer\ItemPresetsProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\PageTsConfig::class,
                \TYPO3\CMS\Wireframe\Form\Data\GridContainer\ItemsTcaProvider::class
            ]
        ]
    ]
);

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['pageContentCreation'] = array_merge(
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['contentCreation'],
    [
        \TYPO3\CMS\Wireframe\Form\Data\PageLayout\ItemPresetsProvider::class => [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInline::class
            ],
            'before' => [
                \TYPO3\CMS\Wireframe\Form\Data\LayoutContainer\ItemPresetsProvider::class,
                \TYPO3\CMS\Wireframe\Form\Data\GridContainer\ItemsProvider::class
            ]
        ]
    ]
);

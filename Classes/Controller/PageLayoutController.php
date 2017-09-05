<?php
declare(strict_types=1);
namespace TYPO3\CMS\Grid\Controller;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Configuration\TranslationConfigurationProvider;
use TYPO3\CMS\Backend\Form\FormDataCompiler;
use TYPO3\CMS\Backend\Form\FormResultCompiler;
use TYPO3\CMS\Backend\Form\NodeFactory;
use TYPO3\CMS\Backend\Module\AbstractModule;
use TYPO3\CMS\Backend\Module\ModuleLoader;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Versioning\VersionState;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Fluid\ViewHelpers\Be\InfoboxViewHelper;
use TYPO3\CMS\Frontend\Page\PageRepository;
use TYPO3\CMS\Grid\Form\Data\ContainerGroup;

/**
 * Controller for Web > Page module
 */
class PageLayoutController extends AbstractModule
{
    /**
     * @var string
     */
    const MODULE_NAMESPACE = 'web';

    /**
     * @var string
     */
    const MODULE_NAME = 'pageLayout';

    /**
     * @deprecated
     */
    const OVERLAY_FLASH_MESSAGE_QUEUE = 'page.layout.%s.flashMessages';

    /**
     * @var TranslationConfigurationProvider
     */
    protected $translationConfigurationProvider;

    /**
     * @var StandaloneView
     */
    protected $view;

    /**
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->translationConfigurationProvider = GeneralUtility::makeInstance(TranslationConfigurationProvider::class);
        $this->view = GeneralUtility::makeInstance(StandaloneView::class);
    }

    /**
     * Request dispatcher
     *
     * @param ServerRequestInterface $request PSR7 Request Object
     * @param ResponseInterface $response PSR7 Response Object
     * @return ResponseInterface
     * @throws \InvalidArgumentException In case an action is not callable
     */
    public function processRequest(ServerRequestInterface $request, ResponseInterface $response)
    {
        $this->request = $request;
        $this->response = $response;

        $this->initializeParameters();

        $methodName = $this->request->getQueryParams()['action'] . 'Action';

        if (!is_callable([$this, $methodName])) {
            throw new \InvalidArgumentException(
                'The method "' . $methodName . '" is not callable within "' . get_class($this) . '".',
                1442736343
            );
        }

        $this->initializeView();

        $this->{$methodName}();

        if ($this->response->getStatusCode() === 200) {
            $this->moduleTemplate->setContent($this->view->render());
            $this->response->getBody()->write($this->moduleTemplate->renderContent());
        }

        return $this->response;
    }

    /**
     * Shows the default layout view
     *
     * @return void
     */
    public function indexAction()
    {
        $queryParameters = $this->request->getQueryParams();
        $pageUid = (int)$queryParameters['page'];
        $languageUid = (int)($queryParameters['language'] ?? 0);

        $formData = $this->compileFormData(
            $pageUid,
            [
                'customData' => [
                    'tx_grid' => [
                        'additionalLanguages' => [$languageUid]
                    ]
                ]
            ],
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['pageLayout']
        );

        $this->prepareOverlayData($formData);
        $this->collectOverlayFlashMessages([$languageUid]);

        $formResult = $this->createFormResult(array_merge_recursive(
            [
                'renderType' => 'layoutContainer'
            ],
            $formData
        ));

        $this->view->assignMultiple([
            'info' => $formData['customData']['tx_grid']['info'],
            'title' => $formData['recordTitle'],
            'form' => [
                'before' => $formResult['before'],
                'after' => $formResult['after'],
                'content' => $formResult['html'],
                'action' => $this->getActionUrl('index', [
                    'page' => $pageUid,
                    'language' => $languageUid
                ])
            ]
        ]);

        $this->createSidebar($pageUid, $formData);
        $this->createActions($pageUid, $languageUid);
        $this->prepareDocumentHeader($formData);
    }

    /**
     * Shows the overlay layout view
     *
     * @return void
     */
    public function overlayAction()
    {
        $queryParameters = $this->request->getQueryParams();
        $pageUid = (int)$queryParameters['page'];
        $languageUid = (int)($queryParameters['language'] ?? 0);

        $translationInfo = $this->translationConfigurationProvider->translationInfo('pages', $pageUid);
        $languages = $languageUid > 0 ? [$languageUid] : array_keys($translationInfo['translations']);

        $formData = $this->compileFormData(
            $pageUid,
            [
                'customData' => [
                    'tx_grid' => [
                        'additionalLanguages' => $languages
                    ]
                ]
            ],
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['pageLayout']
        );

        $this->collectOverlayFlashMessages(array_merge([0], $languages));

        $formResult = $this->createFormResult(array_merge_recursive(
            [
                'renderType' => 'localizationContainer'
            ],
            $formData
        ));

        $this->view->assignMultiple([
            'info' => $formData['customData']['tx_grid']['info'],
            'title' => $formData['recordTitle'],
            'actions' => $formData['customData']['tx_grid']['actions'],
            'form' => [
                'before' => $formResult['before'],
                'after' => $formResult['after'],
                'content' => $formResult['html'],
                'action' => $this->getActionUrl('translate', [
                    'page' => $pageUid,
                    'language' => $languageUid
                ])
            ]
        ]);

        $this->createSidebar($pageUid, $formData);
        $this->createActions($pageUid, 0);
        $this->prepareDocumentHeader($formData);
    }

    /**
     * Shows info about what to do
     *
     * @return void
     */
    public function infoAction()
    {
        $this->view->assignMultiple([
            'info' => [
                'title' => $this->getLanguageService()->sL(
                    'LLL:typo3/sysext/backend/Resources/Private/Language/locallang_layout.xlf:clickAPage_header'
                ),
                'message' => $this->getLanguageService()->sL(
                    'LLL:typo3/sysext/backend/Resources/Private/Language/locallang_layout.xlf:clickAPage_content'
                ),
                'state' => InfoboxViewHelper::STATE_INFO
            ],
            'title' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename']
        ]);
    }

    /**
     * Initializes the request parameters
     *
     * @return void
     */
    protected function initializeParameters()
    {
        $queryParameters = $this->request->getQueryParams();
        $sessionParameters = $this->getBackendUserAuthentication()->getModuleData(
            self::MODULE_NAMESPACE . '_' . self::MODULE_NAME
        );

        if (is_array(GeneralUtility::_GP('SET'))) {
            $sessionParameters = array_merge(
                $sessionParameters,
                array_intersect_key(
                    GeneralUtility::_GP('SET'),
                    array_flip(['action', 'page', 'language'])
                )
            );
        }

        if (empty($queryParameters['action'])) {
            $queryParameters['action'] = empty($sessionParameters['action']) ? 'index' : $sessionParameters['action'];
        }

        $queryParameters['page'] = (int)($queryParameters['page'] ?: GeneralUtility::_GP('id'));
        $queryParameters['language'] = (int)($queryParameters['language'] ?? $sessionParameters['language']);

        $sessionParameters = array_intersect_key(
            $queryParameters,
            array_flip(['action', 'page', 'language'])
        );

        $this->getBackendUserAuthentication()->pushModuleData(
            self::MODULE_NAMESPACE . '_' . self::MODULE_NAME,
            $sessionParameters,
            false
        );

        $GLOBALS['SOBE'] = (object)[
            'MOD_SETTINGS' => $sessionParameters
        ];

        if ($queryParameters['page'] < 1) {
            $queryParameters['action'] = 'info';
        } else {

        }

        $this->request = $this->request->withQueryParams($queryParameters);
    }

    /**
     * Initializes the view
     *
     * @return void
     * @todo Sidebar integration is too bloated
     * @todo Scrollbar of backend modules is partially hidden by the module header
     */
    protected function initializeView()
    {
        $queryParameters = $this->request->getQueryParams();

        $this->view->setTemplatePathAndFilename(
            'EXT:grid/Resources/Private/Templates/PageLayout/' . ucfirst($queryParameters['action']) . '.html'
        );
        $this->view->setPartialRootPaths(['EXT:grid/Resources/Private/Partials']);

        $this->view->assign('parameters', $queryParameters);

        $this->moduleTemplate->getView()->setTemplateRootPaths(['EXT:grid/Resources/Private/Templates']);
    }

    /**
     * @param int $pageUid
     * @param array $additionalParameters
     * @param array $containerProviderList
     * @return array
     */
    protected function compileFormData($pageUid, array $additionalParameters = [], array $containerProviderList = []) : array
    {
        $formDataGroup = GeneralUtility::makeInstance(ContainerGroup::class);
        $formDataCompiler = GeneralUtility::makeInstance(FormDataCompiler::class, $formDataGroup);
        $formDataCompilerInput = array_merge_recursive([
            'tableName' => 'pages',
            'vanillaUid' => $pageUid,
            'command' => 'edit',
            'returnUrl' => $this->getActionUrl($this->request->getQueryParams()['action']),
            'columnsToProcess' => ['content'],
            'customData' => [
                'tx_grid' => [
                    'columnToProcess' => 'content',
                    'containerProviderList' => $containerProviderList
                ]
            ]
        ], $additionalParameters);

        return $formDataCompiler->compile($formDataCompilerInput);
    }

    /**
     * @param $formData
     * @return array
     * @throws \TYPO3\CMS\Backend\Form\Exception
     */
    protected function createFormResult($formData)
    {
        $formResultCompiler = GeneralUtility::makeInstance(FormResultCompiler::class);
        $nodeFactory = GeneralUtility::makeInstance(NodeFactory::class);

        $formResult = $nodeFactory->create($formData)->render();

        $formResultCompiler->mergeResult($formResult);

        $formResultCompiler->addCssFiles();
        $formResult['after'] = $formResultCompiler->printNeededJSFunctions();

        return $formResult;
    }

    /**
     * Returns the language service
     *
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }

    /**
     * Returns the current backend user
     *
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected function getBackendUserAuthentication()
    {
        return $GLOBALS['BE_USER'];
    }

    /**
     * Creates the URI for a backend action
     *
     * @param string $action
     * @param array $parameters
     * @return string
     */
    protected function getActionUrl($action, $parameters = [])
    {
        $parameters += [
            'action' => $action,
            'M' => GeneralUtility::_GP('M')
        ] + $this->request->getQueryParams();

        return GeneralUtility::makeInstance(UriBuilder::class)
            ->buildUriFromModule($parameters['M'], array_filter($parameters, function($value) {
                return !empty($value) || $value === 0;
            }));
    }

    /**
     * Generates the sidebar
     *
     * @param int $page
     * @param array $formData
     * @return void
     */
    protected function createSidebar($page, array $formData = null)
    {
        if ($formData === null) {
            $formData = $this->compileFormData(
                $page,
                [],
                $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['pageLayout']
            );
        }
        $this->moduleTemplate->getView()->assign(
            'sidebar',
            $this->createFormResult(array_merge(
                ['renderType' => 'contentPresetSidebarContainer'],
                $formData
            ))
        );
    }

    /**
     * Prepares the document header
     *
     * @param array $formData
     * @return void
     */
    protected function prepareDocumentHeader(array $formData)
    {
        $pagePaths = BackendUtility::getRecordPath(
            $formData['vanillaUid'],
            $this->getBackendUserAuthentication()->getPagePermsClause(1),
            15,
            1000
        );
        $this->moduleTemplate->getDocHeaderComponent()->setMetaInformation(
            array_merge(
                $formData['databaseRow'],
                [
                    '_thePath' => $pagePaths[0],
                    '_thePathFull' => $pagePaths[1]
                ]
            )
        );

        $this->createMenus($formData);
        $this->createButtons($formData);
    }

    /**
     * Creates the buttons in the document header
     *
     * @param array $formData
     * @return void
     */
    protected function createButtons(array $formData)
    {
        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);
        $languageService = $this->getLanguageService();
        $buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();

        if ($formData['customData']['tx_grid']['actions']['view']) {
            $buttonBar->addButton(
                $buttonBar->makeLinkButton()
                    ->setOnClick($formData['customData']['tx_grid']['actions']['view']['handler']['click'])
                    ->setTitle($languageService->sL('LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.showPage'))
                    ->setIcon($iconFactory->getIcon('actions-view-page', Icon::SIZE_SMALL))
                    ->setHref('#'),
                ButtonBar::BUTTON_POSITION_LEFT,
                3
            );
        }

        if (!$formData['pageTsConfig']['mod.']['web_layout.']['disableIconToolbar'] && $formData['customData']['tx_grid']['actions']['edit']) {
            $this->createOverlayButtons($formData);

            $buttonBar->addButton(
                $buttonBar->makeLinkButton()
                    ->setHref($formData['customData']['tx_grid']['actions']['edit']['url'])
                    ->setTitle($languageService->sL('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:editPageProperties'))
                    ->setIcon($iconFactory->getIcon('actions-page-open', Icon::SIZE_SMALL)),
                ButtonBar::BUTTON_POSITION_LEFT,
                3
            );
        }

        if (!$formData['pageTsConfig']['mod.']['web_layout.']['disableAdvanced']) {
            $buttonBar->addButton(
                $buttonBar->makeLinkButton()
                    ->setHref(BackendUtility::getModuleUrl('tce_db', ['cacheCmd' => $formData['vanillaUid'], 'redirect' => $formData['returnUrl']]))
                    ->setTitle($languageService->sL('LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.clear_cache'))
                    ->setIcon($iconFactory->getIcon('actions-system-cache-clear', Icon::SIZE_SMALL)),
                ButtonBar::BUTTON_POSITION_RIGHT,
                1
            );
        }

        $buttonBar->addButton(
            $buttonBar->makeShortcutButton()
                ->setModuleName(self::MODULE_NAMESPACE . '_' . self::MODULE_NAME)
                ->setGetVariables([
                    'M',
                    'id',
                    'action',
                    'page',
                    'language'
                ])
                ->setSetVariables([
                    'action',
                    'page',
                    'language'
                ])
        );

        $buttonBar->addButton(
            $buttonBar->makeHelpButton()
                ->setModuleName('_MOD_web_layout')
                ->setFieldName('columns_' . ($this->request->getQueryParams()['action'] === 'overlay' ? '2' : '1'))
        );
    }

    /**
     * Creates the menus in the document header
     *
     * @param array $formData
     * @return void
     */
    protected function createMenus(array $formData)
    {
        $actions = [
            'Columns' => 'index',
            'Languages' => 'overlay'
        ];

        $menu = $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
        $menu->setIdentifier('actionMenu');

        foreach ($actions as $label => $action) {
            $menu->addMenuItem(
                $menu->makeMenuItem()
                    ->setTitle($label)
                    ->setHref(
                        $this->getActionUrl($action, ['page' => $formData['vanillaUid']])
                    )
                    ->setActive(
                        $this->request->getQueryParams()['action'] === $action
                    )
            );
        }

        $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);

        $translationInfo = $this->translationConfigurationProvider->translationInfo('pages', $formData['vanillaUid']);
        $languages = [$formData['systemLanguageRows'][0]] + array_intersect_key(
            $formData['systemLanguageRows'],
            $translationInfo['translations']
        );

        if (count($languages) > 1) {
            $menu = $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
            $menu->setIdentifier('languageMenu');

            foreach ($languages as $language) {
                $menu->addMenuItem(
                    $menu->makeMenuItem()
                        ->setTitle($language['title'])
                        ->setHref($this->getActionUrl($this->request->getQueryParams()['action'], [
                            'page' => $formData['vanillaUid'],
                            'language' => $language['uid']
                        ]))
                        ->setActive((int)$this->request->getQueryParams()['language'] === $language['uid'])
                );
            }

            $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);
        }
    }

    /**
     * @param $pageUid
     * @param $languageUid
     * @todo Check access rights
     */
    protected function createActions($pageUid, $languageUid)
    {
        $this->moduleTemplate->getPageRenderer()->loadRequireJsModule(
            'TYPO3/CMS/Backend/PageActions',
            'function(PageActions) {
                PageActions.setPageId(' . $pageUid . ');
                PageActions.setLanguageOverlayId(' . $languageUid . ');
                PageActions.initializePageTitleRenaming();
            }'
        );
    }

    /**
     * @param array $formData
     * @deprecated
     * @see https://review.typo3.org/51272
     */
    protected function prepareOverlayData(array &$formData)
    {
        if (!empty($formData['customData']['tx_grid']['overlays'])) {
            $overlay = $formData['customData']['tx_grid']['overlays'][0];
            $formData['customData']['tx_grid']['items']['children'] = $overlay['customData']['tx_grid']['items']['children'];
            $formData['customData']['tx_grid']['template'] = $overlay['customData']['tx_grid']['template'];
            $formData['recordTitle'] = $overlay['recordTitle'];
        }
    }

    /**
     * @param array $languages
     * @deprecated
     * @see https://review.typo3.org/51272
     */
    protected function collectOverlayFlashMessages(array $languages)
    {
        $service = GeneralUtility::makeInstance(FlashMessageService::class);

        foreach ($languages as $language) {
            $messages = $service->getMessageQueueByIdentifier(sprintf(self::OVERLAY_FLASH_MESSAGE_QUEUE, $language))->getAllMessagesAndFlush();
            foreach ($messages as $message) {
                $service->getMessageQueueByIdentifier()->addMessage($message);
            }
        }
    }

    /**
     * @param array $formData
     * @deprecated
     * @see https://review.typo3.org/51272
     */
    protected function createOverlayButtons(array $formData)
    {
        if ($this->request->getQueryParams()['action'] === 'index' && !empty($formData['customData']['tx_grid']['overlays'])) {
            $this->moduleTemplate->getDocHeaderComponent()->getButtonBar()->addButton(
                $this->moduleTemplate->getDocHeaderComponent()->getButtonBar()->makeLinkButton()
                    ->setHref(BackendUtility::getModuleUrl('record_edit', [
                        'edit' => [
                            $formData['customData']['tx_grid']['overlays'][0]['tableName'] => [
                                $formData['customData']['tx_grid']['overlays'][0]['vanillaUid'] => 'edit'
                            ]
                        ],
                        'returnUrl' => $formData['returnUrl']
                    ]))
                    ->setTitle($this->getLanguageService()->sL('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:editPageLanguageOverlayProperties'))
                    ->setIcon(GeneralUtility::makeInstance(IconFactory::class)->getIcon('mimetypes-x-content-page-language-overlay', Icon::SIZE_SMALL)),
                ButtonBar::BUTTON_POSITION_LEFT,
                3
            );
        }
    }
}

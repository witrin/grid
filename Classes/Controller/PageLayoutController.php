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
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Grid\Form\Data\GridContainerGroup;

/**
 * Controller for Web > Page module
 */
class PageLayoutController extends AbstractModule
{

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
     * @var array
     */
    protected $cache = [];

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
     * Central Request Dispatcher
     *
     * @param ServerRequestInterface $request PSR7 Request Object
     * @param ResponseInterface $response PSR7 Response Object
     *
     * @return ResponseInterface
     *
     * @throws \InvalidArgumentException In case an action is not callable
     */
    public function processRequest(ServerRequestInterface $request, ResponseInterface $response)
    {
        $this->request = $request;
        $this->response = $response;

        $this->initializeAction();

        if (!isset($this->request->getQueryParams()['action'])) {
            $this->request = $this->request->withQueryParams(
                array_merge($this->request->getQueryParams(), ['action' => 'index'])
            );
        }

        $methodName = $this->request->getQueryParams()['action'] . 'Action';

        if (!is_callable([$this, $methodName])) {
            throw new \InvalidArgumentException(
                'The method "' . $methodName . '" is not callable within "' . get_class($this) . '".',
                1442736343
            );
        }

        $this->initializeView();

        $this->{$methodName}();

        if ($this->response->getStatusCode() == 200) {
            $this->moduleTemplate->setContent($this->view->render());
            $this->response->getBody()->write($this->moduleTemplate->renderContent());
        }

        return $this->response;
    }

    /**
     * Index action
     *
     * @return void
     */
    public function indexAction()
    {
        $params = $this->request->getQueryParams();
        $pageUid = (int)$params['page'];
        $languageUid = (int)($params['language'] ?? 0);

        if ($pageUid > 0) {
            $formData = array_merge_recursive(
                [
                    'renderData' => [
                        'languageUid' => $languageUid
                    ],
                    'renderType' => 'layoutContainer'
                ],
                $this->compileFormData(
                    $pageUid,
                    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['pageLayout'],
                    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['pageContent']
                )
            );

            $formResult = $this->createFormResult($formData);

            $this->view->assignMultiple([
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
        } else {
            $this->view->assignMultiple([
                'infoBox' => [
                    'title' => 'Help',
                    'message' => '...'
                ]
            ]);
        }
    }

    /**
     * Translate action
     *
     * @return void
     */
    public function translateAction()
    {
        $params = $this->request->getQueryParams();
        $pageUid = (int)$params['page'];
        $languageUid = (int)($params['language'] ?? 0);

        if ($pageUid > 0) {
            $translationInfo = $this->translationConfigurationProvider->translationInfo('pages', $pageUid);
            $languages = $languageUid > 0 ? [$languageUid] : array_keys($translationInfo['translations']);
            $formData = array_merge_recursive(
                [
                    'renderType' => 'localizationContainer',
                    'renderData' => [
                        'languageUid' => 0,
                        'languageOverlayUids' => array_merge([0], $languages)
                    ]
                ],
                $this->compileFormData(
                    $pageUid,
                    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['pageLayout']
                )
            );

            $formResult = $this->createFormResult($formData);

            $this->view->assignMultiple([
                'title' => $formData['recordTitle'],
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
        } else {
            $this->view->assignMultiple([
                'infoBox' => [
                    'title' => 'Help',
                    'message' => '...'
                ]
            ]);
        }
    }

    /**
     * @param int $pageUid
     * @param array $containerProviderList
     * @param array $itemProviderList
     * @return array
     * @internal param array $itemsProviderList
     */
    protected function compileFormData($pageUid, array $containerProviderList = []) : array
    {
        $hash = md5($pageUid . serialize($containerProviderList));

        if (!$this->cache[$hash]) {
            $formDataGroup = GeneralUtility::makeInstance(GridContainerGroup::class);
            $formDataCompiler = GeneralUtility::makeInstance(FormDataCompiler::class, $formDataGroup);
            $formDataCompilerInput = [
                'tableName' => 'pages',
                'vanillaUid' => $pageUid,
                'command' => 'edit',
                'returnUrl' => $this->getActionUrl(null, []),
                'columnsToProcess' => ['content'],
                'customData' => [
                    'tx_grid' => [
                        'columnToProcess' => 'content',
                        'containerProviderList' => $containerProviderList
                    ]
                ]
            ];
            $this->cache[$hash] = $formDataCompiler->compile($formDataCompilerInput);
        }

        return $this->cache[$hash];
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
     * Initializes the arguments
     *
     * @return void
     */
    protected function initializeAction()
    {
        $params = $this->request->getQueryParams();
        $sessionData = $this->getBackendUserAuthentication()->getSessionData(self::class);

        if (!isset($params['language'])) {
            $params['language'] = (int)$sessionData['language'];
        } else {
            $sessionData['language'] = $params['language'];
        }

        if (!isset($params['page'])) {
            $params['page'] = (int)GeneralUtility::_GP('id');
        }

        if (isset($params['action'])) {
            $sessionData['action'] = $params['action'];
        }

        $this->getBackendUserAuthentication()->setAndSaveSessionData(self::class, $sessionData);

        if ($sessionData['action'] && $sessionData['action'] !== $params['action']) {
            $params['action'] = $sessionData['action'];
        }

        $this->request = $this->request->withQueryParams($params);
    }

    /**
     * Set up the view
     *
     * @return void
     */
    protected function initializeView()
    {
        $params = $this->request->getQueryParams();

        $this->view->setTemplatePathAndFilename(
            'EXT:grid/Resources/Private/Templates/PageLayout/' . ucfirst($params['action']) . '.html'
        );
        // @todo This is nasty! There must be a better way to append the sidebar markup!
        $this->moduleTemplate->getView()->setLayoutRootPaths(['EXT:grid/Resources/Private/Layouts']);
        $this->moduleTemplate->getView()->setTemplateRootPaths(['EXT:grid/Resources/Private/Templates']);

        //$this->moduleTemplate->setFlashMessageQueue($this->controllerContext->getFlashMessageQueue());

        if (isset($params['page']) && (int)$params['page'] > 0) {
            $this->createMenus((int)$params['page']);
            $this->createSidebar((int)$params['page']);

            // @todo Check access rights
            // @todo Language overlay id
            $this->moduleTemplate->getPageRenderer()->loadRequireJsModule('TYPO3/CMS/Backend/PageActions', 'function(PageActions) {
                PageActions.setPageId(' . (int)$params['page'] . ');
                PageActions.setLanguageOverlayId(0);
                PageActions.initializePageTitleRenaming();
            }');
        }
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
     */
    protected function createSidebar($page)
    {
        $formData = $this->compileFormData(
            $page,
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['pageLayout']
        );
        $this->moduleTemplate->getView()->assign(
            'sidebar',
            $this->createFormResult(array_merge(
                ['renderType' => 'contentPresetSidebarContainer'],
                $formData
            ))
        );
    }

    /**
     * Generates the menus
     *
     * @param int $page
     * @return void
     */
    protected function createMenus($page)
    {
        $actions = [
            'Columns' => 'index',
            'Languages' => 'translate'
        ];

        $menu = $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
        $menu->setIdentifier('actionMenu');

        foreach ($actions as $label => $action) {
            $menu->addMenuItem(
                $menu->makeMenuItem()
                    ->setTitle($label)
                    ->setHref(
                        $this->getActionUrl($action, ['page' => $page])
                    )
                    ->setActive(
                        $this->request->getQueryParams()['action'] === $action
                    )
            );
        }

        $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);

        $translationInfo = $this->translationConfigurationProvider->translationInfo('pages', $page);
        $languages = $this->translationConfigurationProvider->getSystemLanguages($page);

        uasort($languages, function ($a, $b) {
            return $a['sorting'] <=> $b['sorting'];
        });

        $languages = [$languages[0]] + array_intersect_key(
            $languages,
            $translationInfo['translations']
        );

        $menu = $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
        $menu->setIdentifier('languageMenu');

        foreach ($languages as $language) {
            $menu->addMenuItem(
                $menu->makeMenuItem()
                    ->setTitle($language['title'])
                    ->setHref($this->getActionUrl($this->request->getQueryParams()['action'], [
                        'page' => $page,
                        'language' => $language['uid']
                    ]))
                    ->setActive((int)$this->request->getQueryParams()['language'] === $language['uid'])
            );
        }

        $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);
    }
}

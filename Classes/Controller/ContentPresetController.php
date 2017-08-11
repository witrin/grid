<?php
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
use TYPO3\CMS\Backend\Form\FormDataCompiler;
use TYPO3\CMS\Backend\Form\FormResultCompiler;
use TYPO3\CMS\Backend\Form\NodeFactory;
use TYPO3\CMS\Backend\Module\AbstractModule;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Grid\Form\Data\GridContainerGroup;
use TYPO3\CMS\Grid\Utility\TcaUtility;

/**
 * Controller for content element wizard
 */
class ContentPresetController extends AbstractModule
{
    /**
     * Legacy interface
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @deprecated
     */
    public function index(ServerRequestInterface $request, ResponseInterface $response)
    {
        $params = $request->getQueryParams();
        $request = $request->withQueryParams([
            'containerUid' => $params['id'],
            'languageUid' => $params['sys_language_uid'],
            'containerTable' => 'pages',
            'containerField' => 'content',
            'returnUrl' => $params['returnUrl'],
            'areaUid' => $params['colPos'],
            'ancestorUid' => $params['uid_pid'] < 0 ? abs((int)$params['uid_pid']) : null,
            'pageUid' => $params['uid_pid'] > 0 ? (int)$params['uid_pid'] : null
        ]);

        return $this->indexAction($request, $response);
    }

    /**
     * Creates a new content element
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \TYPO3\CMS\Backend\Form\Exception
     * @todo Deprecate `pageUid`
     */
    public function indexAction(ServerRequestInterface $request, ResponseInterface $response)
    {
        $parameters = $request->getQueryParams();

        $containerUid = (int)$parameters['containerUid'];
        $languageUid = isset($parameters['languageUid']) ? (int)$parameters['languageUid'] : 0;
        $ancestorUid = isset($parameters['ancestorUid']) ? (int)$parameters['ancestorUid'] : null;
        $areaUid = isset($parameters['areaUid']) ? (int)$parameters['areaUid'] : null;
        $returnUrl = GeneralUtility::sanitizeLocalUrl($parameters['returnUrl']);
        $pageUid = isset($parameters['pageUid']) ? (int)$parameters['pageUid'] : null;

        $formDataGroup = GeneralUtility::makeInstance(GridContainerGroup::class);
        $formDataCompiler = GeneralUtility::makeInstance(FormDataCompiler::class, $formDataGroup);
        $formDataCompilerInput = [
            'tableName' => (string)$parameters['containerTable'],
            'vanillaUid' => $containerUid,
            'command' => 'edit',
            'returnUrl' => $returnUrl,
            'columnsToProcess' => [(string)$parameters['containerField']],
            'customData' => [
                'tx_grid' => [
                    'columnToProcess' => (string)$parameters['containerField'],
                        'containerProviderList' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup'][((string)$parameters['containerTable'] === 'pages'
                            ? 'pageContentCreation' : 'contentCreation')]
                ]
            ]
        ];
        $formData = array_merge([
            'languageUid' => $languageUid
        ], $formDataCompiler->compile($formDataCompilerInput));

        $formResultCompiler = GeneralUtility::makeInstance(FormResultCompiler::class);
        $formResult = GeneralUtility::makeInstance(NodeFactory::class)->create(array_merge(
            ['renderType' => 'contentPresetTabContainer'],
            $formData
        ))->render();
        $formResultCompiler->mergeResult($formResult);

        $itemsConfig = $formData['customData']['tx_grid']['items']['config'];
        $vanillaItemsTca = $formData['customData']['tx_grid']['items']['vanillaTca'];

        $values = array_merge([
            $itemsConfig['grid_area_field'] => $areaUid,
            $vanillaItemsTca['ctrl']['languageField'] => $languageUid
        ], $formData['customData']['tx_grid']['itemsDefaultValues']);
        $pageUid = isset($pageUid) ? $pageUid : $formData['effectivePid'];

        if ($returnUrl) {
            $buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();
            $buttonBar->addButton(
                $buttonBar->makeLinkButton()
                    ->setHref($returnUrl)
                    ->setTitle($this->getLanguageService()->sL('LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.goBack'))
                    ->setIcon($this->moduleTemplate->getIconFactory()->getIcon('actions-view-go-back', Icon::SIZE_SMALL))
            );
        }

        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplatePathAndFilename('EXT:grid/Resources/Private/Templates/ContentPreset/Index.html');

        $view->assignMultiple([
            'form' => [
                'content' => $formResult['html'],
                'after' => $formResultCompiler->printNeededJSFunctions(),
                'action' => BackendUtility::getModuleUrl(
                    'record_edit',
                    [
                        'edit' => [
                            $itemsConfig['foreign_table'] => [
                                $ancestorUid !== null ? '-' . $ancestorUid : $pageUid => 'new'
                            ]
                        ],
                        'defVals' => [
                            $itemsConfig['foreign_table'] => TcaUtility::filterHiddenFields($vanillaItemsTca['columns'], $values)
                        ],
                        'overrideVals' => [
                            $itemsConfig['foreign_table'] => array_diff_key($values, TcaUtility::filterHiddenFields($vanillaItemsTca['columns'], $values))
                        ],
                        'returnUrl' => $returnUrl
                    ]
                )
            ]
        ]);

        $formResultCompiler->addCssFiles();
        $this->moduleTemplate->setContent($view->render());

        $response->getBody()->write($this->moduleTemplate->renderContent());

        return $response;
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
}

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
use TYPO3\CMS\Backend\Form\FormDataCompiler;
use TYPO3\CMS\Backend\Form\FormResultCompiler;
use TYPO3\CMS\Backend\Form\NodeFactory;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Grid\Form\Data\ContainerGroup;

/**
 * Wizard controller for new content
 */
class ContentWizardController extends AbstractController
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
            'ancestorUid' => $params['uid_pid'] < 0 ? abs((int)$params['uid_pid']) : null
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
     */
    public function indexAction(ServerRequestInterface $request, ResponseInterface $response)
    {
        $parameters = $request->getQueryParams();
        $parameters['returnUrl'] = GeneralUtility::sanitizeLocalUrl($parameters['returnUrl']);
        $parameters['context'] = in_array($parameters['context'], ['modal', 'module']) ? $parameters['context'] : 'modal';

        $itemsConfiguration = $GLOBALS['TCA'][(string)$parameters['containerTable']]['columns'][(string)$parameters['containerField']];

        $formDataGroup = GeneralUtility::makeInstance(ContainerGroup::class);
        $formDataCompiler = GeneralUtility::makeInstance(FormDataCompiler::class, $formDataGroup);
        $formDataCompilerInput = [
            'tableName' => (string)$parameters['containerTable'],
            'vanillaUid' => (int)$parameters['containerUid'],
            'command' => 'edit',
            'returnUrl' => $parameters['returnUrl'],
            'columnsToProcess' => [(string)$parameters['containerField']],
            'customData' => [
                'tx_grid' => [
                    'columnToProcess' => (string)$parameters['containerField'],
                    'containerProviderList' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup'][
                        ((string)$parameters['containerTable'] === 'pages' ? 'pageLayout' : 'contentContainer')
                    ],
                    'items' => [
                        'defaultValues' => [
                            $GLOBALS['TCA'][$itemsConfiguration['foreign_table']]['ctrl']['languageField'] => (int)$parameters['languageUid'] ?? 0
                        ] + ($parameters['areaUid'] ? [$itemsConfiguration['config']['foreign_area_field'] => (int)$parameters['areaUid']] : [])
                    ]
                ]
            ]
        ];
        $formData = $formDataCompiler->compile($formDataCompilerInput);

        $formResultCompiler = GeneralUtility::makeInstance(FormResultCompiler::class);
        $formResult = GeneralUtility::makeInstance(NodeFactory::class)->create(array_merge_recursive(
            [
                'renderType' => 'contentWizardContainer',
                'renderData' => [
                    'context' => $parameters['context'],
                    'steps' => array_merge(['presets'], (array_key_exists('areaUid', $parameters) ? [] : ['positions'])),
                    'url' => BackendUtility::getModuleUrl(
                        'record_edit',
                        [
                            'returnUrl' => $parameters['returnUrl']
                        ]
                    ),
                    'parameters' => [
                        'edit' => [
                            $formData['customData']['tx_grid']['items']['config']['foreign_table'] => [
                                $parameters['ancestorUid'] ? '-' . (int)$parameters['ancestorUid'] : $formData['effectivePid'] => 'new'
                            ]
                        ]
                    ]
                ]
            ],
            $formData
        ))->render();
        $formResultCompiler->mergeResult($formResult);

        if ($parameters['context'] === 'module' && $returnUrl) {
            $buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();
            $buttonBar->addButton(
                $buttonBar->makeLinkButton()
                    ->setHref($returnUrl)
                    ->setTitle($this->getLanguageService()->sL('LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.goBack'))
                    ->setIcon($this->moduleTemplate->getIconFactory()->getIcon('actions-view-go-back', Icon::SIZE_SMALL))
            );
        }

        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplatePathAndFilename('EXT:grid/Resources/Private/Templates/ContentWizard/Index.html');

        $view->assignMultiple([
            'form' => [
                'content' => $formResult['html'],
                'after' => $formResultCompiler->printNeededJSFunctions(),
                'action' => '#'
            ]
        ]);

        $formResultCompiler->addCssFiles();

        if ($parameters['context'] === 'module') {
            $this->moduleTemplate->setContent($view->render());
            $response->getBody()->write($this->moduleTemplate->renderContent());
        } else {
            $response->getBody()->write($view->render());
        }

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

<?php
declare(strict_types=1);
namespace TYPO3\CMS\Grid\Form\Node;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Grid\Utility\GridUtility;
use TYPO3Fluid\Fluid\View\ViewInterface;

/**
 * Render the layout of a content container
 *
 * This is an entry container called from controllers.
 */
class LayoutContainer extends AbstractContainer
{
    /**
     * @var string
     */
    protected $templatePathAndFileName = 'EXT:grid/Resources/Private/Templates/Form/Node/LayoutContainer.html';

    /**
     * @var array
     */
    protected $partialRootPaths = ['EXT:grid/Resources/Private/Partials/Form/Node/LayoutContainer/'];

    /**
     * @var string
     */
    protected $itemRenderType = 'contentPreview';

    /**
     * Entry method
     *
     * @return array As defined in initializeResultArray() of AbstractNode
     */
    public function render()
    {
        $result = $this->initializeResultArray();
        $view = $this->initializeView();

        foreach ($this->items() as &$item) {
            $this->prepareItem($item);

            $item['renderData'] += $this->nodeFactory->create($item)->render();
            $result = $this->mergeChildReturnIntoExistingResult($result, $item['renderData'], false);
        }

        $this->prepareView($view);

        $result['html'] = $view->render();

        return $result;
    }

    /**
     * Generate the items to render
     */
    protected function &items()
    {
        foreach ($this->data['customData']['tx_grid']['items']['children'] as &$item) {
            yield $item;
        }
    }

    /**
     * Prepare the item before rendering it
     *
     * @param array $item
     */
    protected function prepareItem(array &$item) {
        $item['renderType'] = $this->itemRenderType;
        $item['renderData'] = [
            'contentTemplatePathAndFilename' => $item['customData']['tx_grid']['previewTemplate'],
            'showFlag' => $item['customData']['tx_grid']['language']['uid'] > 0,
            'returnUrl' => $this->data['returnUrl'],
            'hasErrors' => $item['customData']['tx_grid']['localization']['status'] === 'unbound' &&
                $this->data['customData']['tx_grid']['localization']['mode'] === 'strict',
            'hasWarnings' => $item['customData']['tx_grid']['area'] === null,
            'displayLegacyActions' => $this->data['displayLegacyActions']
        ];
    }

    /**
     * Prepare the view
     *
     * @param ViewInterface $view
     */
    protected function prepareView(ViewInterface $view)
    {
        $view->assignMultiple(
            $this->mapData($this->data) + [
                'columns' => array_fill(
                    0,
                    $this->data['customData']['tx_grid']['template']['columns'], 100 / ((int)$this->data['customData']['tx_grid']['template']['columns'] ?: 1)
                ),
                'rows' => GridUtility::transformToTable($this->data['customData']['tx_grid']['template']),
                'hidden' => array_filter(iterator_to_array($this->items()), function($item) {
                    return !$item['customData']['tx_grid']['visible'];
                }),
                'unused' => $this->data['customData']['tx_grid']['template']['unused'],
                'settings' => $this->getUserConfiguration()
            ]
        );
    }

    /**
     * @param $data
     * @return array
     */
    protected function mapData($data)
    {
        return [
            'language' => $data['customData']['tx_grid']['language'],
            'areas' => $data['customData']['tx_grid']['template']['areas'],
            'uid' => $data['vanillaUid'],
            'pid' => $data['effectivePid'],
            'actions' => $data['customData']['tx_grid']['actions'],
            'title' => $data['recordTitle'],
            'record' => $this->data['databaseRow'],
            'tca' => [
                'container' => [
                    'table' => $data['tableName'],
                    'field' => $data['customData']['tx_grid']['columnToProcess']
                ],
                'element' => [
                    'table' => $data['customData']['tx_grid']['items']['config']['foreign_table'],
                    'fields' => [
                        'area' => $data['customData']['tx_grid']['items']['config']['foreign_area_field'],
                        'language' => $data['customData']['tx_grid']['items']['vanillaTca']['ctrl']['languageField'],
                        'foreign' => [
                            'table' => $data['customData']['tx_grid']['items']['config']['foreign_table_field'],
                            'field' => $data['customData']['tx_grid']['items']['config']['foreign_field']
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @return ViewInterface
     */
    protected function initializeView(): \TYPO3\CMS\Extbase\Mvc\View\ViewInterface
    {
        $view = parent::initializeView();
        $view->setPartialRootPaths($this->getPartialRootPaths());
        return $view;
    }

    /**
     * @return array
     */
    protected function initializeResultArray(): array
    {
        return array_merge(
            parent::initializeResultArray(),
            [
                'additionalInlineLanguageLabelFiles' => [
                    'EXT:backend/Resources/Private/Language/locallang_layout.xlf'
                ],
                'requireJsModules' => [
                    'TYPO3/CMS/Backend/Tooltip',
                    'TYPO3/CMS/Backend/ContextMenu',
                    'TYPO3/CMS/Backend/Modal',
                    'TYPO3/CMS/Grid/DragDrop',
                    'TYPO3/CMS/Grid/Actions',
                    'TYPO3/CMS/Grid/Localization',
                    'TYPO3/CMS/Grid/Paste',
                    'TYPO3/CMS/Grid/Wizard'
                ],
                'stylesheetFiles' => [
                    'EXT:grid/Resources/Public/Css/DragDrop.css',
                    'EXT:grid/Resources/Public/Css/Layout.css',
                    'EXT:grid/Resources/Public/Css/Wizard.css'
                ]
            ]
        );
    }

    /**
     * Get the template path and filename
     *
     * @return string
     */
    protected function getTemplatePathAndFilename()
    {
        $templatePathAndFilename = $this->data['renderData']['templatePathAndFilename'] ?? $this->templatePathAndFileName;
        return GeneralUtility::getFileAbsFileName($templatePathAndFilename);
    }

    /**
     * Get the partial root paths
     *
     * @return array
     */
    protected function getPartialRootPaths()
    {
        return $this->data['renderData']['partialRootPaths'] ?? $this->partialRootPaths;
    }

    /**
     * Returns the user configuration
     *
     * @return array
     * @todo Is this the right API?
     */
    protected function getUserConfiguration()
    {
        return $this->getBackendUserAuthentication()->uc['tx_grid'][$this->data['tableName']][$this->data['customData']['tx_grid']['columnToProcess']] ?? [];
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
}

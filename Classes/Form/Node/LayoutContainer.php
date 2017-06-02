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
 * Render a content container
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

        foreach ($this->data['customData']['tx_grid']['items'] as &$item) {
            if (!$this->filterItem($item)) {
                $this->prepareItem($item);

                $item['renderData'] += $this->nodeFactory->create($item)->render();
                $result = $this->mergeChildReturnIntoExistingResult($result, $item['renderData'], false);
            }
        }

        $this->prepareView($view);

        $result['html'] = $view->render();

        return $result;
    }

    /**
     * Prepare the item before rendering it
     *
     * @param array $item
     */
    protected function prepareItem(array &$item) {
        $item['renderType'] = $this->itemRenderType;
        $item['renderData'] = [
            'pageLayoutView' => $this->data['pageLayoutView'],
            'showFlag' => $item['customData']['tx_grid']['languageUid'] > 0,
            'returnUrl' => $this->data['returnUrl'],
            'hasErrors' => !$item['customData']['tx_grid']['hasTranslations'] &&
                $item['customData']['tx_grid']['languageUid'] > 0 &&
                !$this->data['allowInconsistentLanguageHandling'],
            'displayLegacyActions' => $this->data['displayLegacyActions']
        ];
    }

    /**
     * Filter the item from being rendered
     *
     * @param array $item
     * @return bool
     */
    protected function filterItem(array &$item) {
        return $item['customData']['tx_grid']['languageUid'] != $this->data['renderData']['languageUid'];
    }

    /**
     * Prepare the view
     *
     * @param ViewInterface $view
     * @todo Full support for `inline` fields
     */
    protected function prepareView(ViewInterface $view)
    {
        $view->assignMultiple([
            'language' => $this->data['systemLanguageRows'][$this->data['renderData']['languageUid'] ?? 0],
            'areas' => $this->data['customData']['tx_grid']['template']['areas'],
            'columns' => array_fill(
                0,
                $this->data['customData']['tx_grid']['template']['columns'], 100 / ((int)$this->data['customData']['tx_grid']['template']['columns'] ?: 1)
            ),
            'rows' => GridUtility::transformToTable($this->data['customData']['tx_grid']['template']),
            'uid' => $this->data['vanillaUid'],
            'tca' => [
                'container' => [
                    'table' => $this->data['tableName'],
                ],
                'element' => [
                    'table' => $this->data['customData']['tx_grid']['itemsConfig']['foreign_table'],
                    'fields' => [
                        'area' => $this->data['customData']['tx_grid']['itemsConfig']['grid_area_field'],
                        'language' => $this->data['customData']['tx_grid']['vanillaItemsTca']['ctrl']['languageField'],
                        'foreign' => [
                            'table' => $this->data['customData']['tx_grid']['itemsConfig']['foreign_table_field'],
                            'field' => $this->data['customData']['tx_grid']['itemsConfig']['foreign_field']
                        ]
                    ]
                ]
            ]
        ]);
    }

    /**
     * @return array
     */
    protected function initializeResultArray(): array
    {
        return array_merge(
            parent::initializeResultArray(),
            [
                'requireJsModules' => [
                    'TYPO3/CMS/Backend/Tooltip',
                    'TYPO3/CMS/Backend/ClickMenu',
                    'TYPO3/CMS/Backend/Modal',
                    'TYPO3/CMS/Grid/DragDrop'
                ],
                'stylesheetFiles' => [
                    'EXT:grid/Resources/Public/Css/DragDrop.css',
                    'EXT:grid/Resources/Public/Css/BackendLayout.css'
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
}

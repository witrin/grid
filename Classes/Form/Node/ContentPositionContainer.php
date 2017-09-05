<?php
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
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Render a table with content element positions of the given grid container
 *
 */
class ContentPositionContainer extends AbstractContainer
{
    /**
     * Entry method
     *
     * @return array As defined in initializeResultArray() of AbstractNode
     * @todo Create some kind of a reusable iterator utility for layouts
     */
    public function render()
    {
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplatePathAndFilename($this->getTemplatePathAndFilename());

        $columns = array_fill(
            0,
            (int)$this->data['processedTca']['backendLayout']['columnCount'],
            ['width' => 100 / (int)$this->data['processedTca']['backendLayout']['columnCount']]
        );
        $rows = [];

        $view->setTemplatePathAndFilename($this->getTemplatePathAndFilename());

        for ($i = 1; $i <= (int)$this->data['processedTca']['backendLayout']['rowCount']; $i++) {
            $row = $this->data['processedTca']['backendLayout']['rows'][$i];
            $cells = [];

            if (empty($row)) {
                continue;
            }

            for ($j = 1; $j <= (int)$this->data['processedTca']['backendLayout']['columnCount']; $j++) {
                $column = $this->data['processedTca']['backendLayout']['columns'][$row['columns'][$j]['position']];
                $elements = [];

                if (empty($column)) {
                    continue;
                }

                if ($column['assigned']) {
                    foreach ((array)$this->data['processedTca']['contentElementPositions'][$column['position']] as $contentElement) {
                        if ($contentElement['processedTca']['languageUid'] === $this->data['languageUid']) {
                            $elements[] = [
                                'table' => $contentElement['tableName'],
                                'data' => $contentElement['databaseRow'],
                                'title' => $contentElement['recordTitle'],
                                'parameters' => $this->createParameters(-$contentElement['vanillaUid'], $column['position'])
                            ];
                        }
                    }
                }

                $cells[] = [
                    'uid' => $column['position'],
                    'title' => $column['name'],
                    'elements' => $elements,
                    'columnSpan' => (int)$column['colspan'],
                    'rowSpan' => (int)$column['rowspan'],
                    'parameters' => $this->createParameters($this->data['vanillaUid'], $column['position'])
                ];
            }

            $rows[] = [
                'cells' => $cells
            ];
        }

        $view->assignMultiple([
            'columns' => $columns,
            'rows' => $rows,
            'uid' => $this->data['vanillaUid']
        ]);

        return array_merge($this->initializeResultArray(), [
            'html' => $view->render()
        ]);
    }

    /**
     * @param int $ancestorUid
     * @param int $areaUid
     * @return string
     */
    protected function createParameters($ancestorUid, $areaUid)
    {
        return GeneralUtility::implodeArrayForUrl(
            '',
            [
                'edit' => [
                    $this->data['customData']['tx_grid']['items']['config']['foreign_table'] => [
                        (string)$ancestorUid => 'new'
                    ]
                ],
                'defVals' => [
                    $this->data['customData']['tx_grid']['items']['config']['grid_area_field'] => $areaUid
                ]
            ],
            '',
            true,
            true
        );
    }

    /**
     * @return string
     */
    protected function getTemplatePathAndFilename()
    {
        return GeneralUtility::getFileAbsFileName(
            'EXT:grid/Resources/Private/Templates/Form/Node/ContentPosition.html'
        );
    }
}

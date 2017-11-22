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
use TYPO3\CMS\Grid\Utility\GridUtility;

/**
 * Render a table with content element positions of the given grid container
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

        $view->assignMultiple([
            'rows' => GridUtility::transformToTable($this->data['customData']['tx_grid']['template']),
            'columns' => array_fill(
                0,
                $this->data['customData']['tx_grid']['template']['columns'],
                100 / ((int)$this->data['customData']['tx_grid']['template']['columns'] ?: 1)
            )
        ]);

        return array_merge($this->initializeResultArray(), [
            'html' => $view->render()
        ]);
    }

    /**
     * @param int $ancestorUid
     * @param int $areaUid
     * @todo Should be part of a data handler
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
                    $this->data['customData']['tx_grid']['items']['config']['foreign_area_field'] => $areaUid
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

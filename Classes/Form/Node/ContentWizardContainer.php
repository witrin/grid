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
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Render a wizard form with content element presets and positions
 *
 * This is an entry container called from controllers.
 */
class ContentWizardContainer extends AbstractContainer
{
    /**
     * @var string
     */
    protected $templatePathAndFileName = 'EXT:grid/Resources/Private/Templates/Form/Node/ContentWizardContainer.html';

    /**
     * Entry method
     *
     * @return array As defined in initializeResultArray() of AbstractNode
     */
    public function render()
    {
        $result = $this->initializeResultArray();
        $view = $this->initializeView();
        $steps = [];

        foreach ((array)$this->data['renderData']['steps'] as $step) {
            $steps[$step] = $this->{'render' . ucfirst($step)}();
        }

        $view->assignMultiple([
            'steps' => $steps,
            'context' => $this->data['renderData']['context'],
            'url' => $this->data['renderData']['url'],
            'parameters' => $this->data['renderData']['parameters']
        ]);

        $result['html'] = $view->render();

        return $result;
    }

    /**
     * Render the presets for selection
     *
     * @return string
     */
    protected function renderPresets()
    {
        $tabs = [];

        foreach ($this->data['customData']['tx_grid']['itemPresets'] as $group) {
            $content = '';

            foreach ($group['elements'] as $element) {
                $formResult = $this->nodeFactory->create([
                    'renderType' => 'contentPreset',
                    'renderData' => $this->data['renderData'] + ['preset' => $element]
                ])->render();

                $content .= $formResult['html'];
            }

            $tabs[] = [
                'label' => $group['header'],
                'content' => $content
            ];
        }

        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplatePathAndFilename(
            GeneralUtility::getFileAbsFileName('EXT:backend/Resources/Private/Templates/DocumentTemplate/Tabs.html')
        );
        $view->assignMultiple([
            'id' => 'DTM-' . GeneralUtility::shortMD5('content-presets-tab-container'),
            'items' => $tabs,
            'defaultTabIndex' => 1,
            'wrapContent' => true,
            'storeLastActiveTab' => true,
        ]);

        return $view->render();
    }

    /**
     * Render the areas for selection
     *
     * @return string
     */
    protected function renderPositions()
    {
        $formResult = $this->nodeFactory->create(array_merge($this->data, [
            'renderType' => 'contentPositionContainer'
        ]))->render();

        return $formResult['html'];
    }

    /**
     * Get the template path
     *
     * @return string
     */
    protected function getTemplatePathAndFilename()
    {
        return GeneralUtility::getFileAbsFileName($this->templatePathAndFileName);
    }

    /**
     * Initialize the resulkt array
     *
     * @return array
     */
    protected function initializeResultArray(): array
    {
        return array_merge(
            parent::initializeResultArray(),
            [
                'requireJsModules' => [
                    'TYPO3/CMS/Backend/Tabs',
                    'TYPO3/CMS/Backend/ContextMenu',
                    'TYPO3/CMS/Grid/Wizard'
                ]
            ]
        );
    }
}

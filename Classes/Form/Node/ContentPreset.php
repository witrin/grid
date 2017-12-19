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

/**
 * Generation of preview of a content elementGenerate
 */
class ContentPreset extends AbstractElement
{
    /**
     * @var string
     */
    protected $templatePathAndFileName = 'EXT:grid/Resources/Private/Templates/Form/Node/ContentPreset.html';

    /**
     * Render the preview
     *
     * @return array As defined in initializeResultArray() of AbstractNode
     */
    public function render()
    {
        $result = $this->initializeResultArray();
        $view = $this->initializeView();

        $view->assignMultiple([
            'preset' => $this->data['renderData']['preset'],
            'data' => array_merge(
                [
                    'parameters' => json_encode($this->data['renderData']['preset']['parameters'])
                ],
                $this->data['renderData']['context'] === 'modal' ?
                    (in_array('positions', (array)$this->data['renderData']['steps']) 
                        ? ['slide' => 'next'] : ['dismiss' => 'modal']) : []
            ),
            'class' => $this->data['renderData']['context'] === 'modal' ? 't3js-grid-content-creation-wizard-item' : ''
        ]);

        $result['html'] = $view->render();

        return $result;
    }

    /**
     * @return string
     */
    protected function getTemplatePathAndFilename()
    {
        return GeneralUtility::getFileAbsFileName($this->templatePathAndFileName);
    }
}

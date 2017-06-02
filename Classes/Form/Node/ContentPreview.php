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

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Service\FlexFormService;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Generates the preview of a content element
 *
 */
class ContentPreview extends AbstractElement
{
    /**
     * @var string
     */
    protected $templatePathAndFileName = 'EXT:grid/Resources/Private/Templates/Form/Node/ContentPreview.html';

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
            'table' => $this->data['tableName'],
            'language' => $this->data['customData']['tx_grid']['languageUid'],
            'flag' => $this->data['renderData']['showFlag'] ? $this->data['systemLanguageRows'][$this->data['customData']['tx_grid']['languageUid']]['flagIconIdentifier'] : '',
            'record' => $this->data['vanillaUid'],
            'actions' => $this->data['customData']['tx_grid']['actions'],
            'wizard' => !$this->data['disableContentElementWizard'],
            'position' => $this->data['customData']['tx_grid']['areaUid'],
            'visible' => $this->data['customData']['tx_grid']['visibility'] === 'visible',
            'errors' => $this->data['renderData']['hasErrors'],
            'data' => $this->data['databaseRow'],
            'content' => $this->renderContent()
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

    /**
     * @return BackendUserAuthentication
     */
    protected function getBackendUserAuthentication()
    {
        return $GLOBALS['BE_USER'];
    }

    /**
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }

    /**
     * @return string
     */
    protected function renderContent()
    {
        try {
            $template = GeneralUtility::getFileAbsFileName($this->data['renderData']['contentTemplatePathAndFilename']);

            if (empty($template)) {
                return null;
            }

            $view = GeneralUtility::makeInstance(StandaloneView::class);
            $view->setTemplatePathAndFilename($template);
            $view->assignMultiple($this->data['databaseRow']);

            if (!empty($this->data['databaseRow']['pi_flexform'])) {
                $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
                $view->assign(
                    'pi_flexform_transformed',
                    $flexFormService->convertFlexFormContentToArray($this->data['databaseRow']['pi_flexform'])
                );
            }
            return $view->render();
        } catch (\Exception $e) {
            // @todo log exception
        }

        return null;
    }
}

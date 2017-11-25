<?php
declare(strict_types=1);
namespace TYPO3\CMS\Grid\Form\Data\Layout;

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
use TYPO3\CMS\Backend\Clipboard\Clipboard;
use TYPO3\CMS\Backend\Form\FormDataProviderInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Grid\Utility\TcaUtility;

/**
 * Resolve paste action URL for the grid container
 */
class PasteItemActionProvider implements FormDataProviderInterface
{
    protected $clipboard;

    /**
     * Add form data to result array
     *
     * @param array $result Initialized result array
     * @return array Result filled with more data
     */
    public function addData(array $result)
    {
        if ($this->isAvailable($result, [])) {
            $table = $result['customData']['tx_grid']['items']['config']['foreign_table'];
            $element = $this->getClipboard()->elFromTable($table);

            if (!empty($element)) {
                $uid = (int)substr(key($element), 11);
                $record = BackendUtility::getRecord($table, $uid);
                $title = BackendUtility::getRecordTitle($table, $record);

                foreach ($result['customData']['tx_grid']['template']['areas'] as &$area) {
                    if ($this->isAvailable($result, ['area' => $area])) {
                        $area['actions']['paste'] = $this->getAttributes(
                            $result,
                            [
                                'area' => $area,
                                'class' => 't3js-grid-paste',
                                'mode' => $this->getClipboard()->clipData['normal']['mode'],
                                'record' => ['uid' => $uid, 'title' => $title],
                                'title' => 'LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:pasteIntoColumn',
                                'section' => 'body'
                            ]
                        );

                        foreach ($area['items'] as &$item) {
                            $item['customData']['tx_grid']['actions']['paste'] = $this->getAttributes(
                                $result,
                                [
                                    'area' => $area,
                                    'item' => $item,
                                    'class' => 't3js-grid-paste',
                                    'mode' => $this->getClipboard()->clipData['normal']['mode'],
                                    'record' => ['uid' => $uid, 'title' => $title],
                                    'title' => 'LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:pasteAfterRecord',
                                    'section' => 'after'
                                ]
                           );
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @param array $result
     * @param array $parameters
     * @return bool
     */
    protected function isAvailable(array $result, array $parameters) : bool
    {
        return (
            $result['customData']['tx_grid']['localization']['mode'] !== 'strict' ||
            empty($result['customData']['tx_grid']['items']['children']) ||
            $result['customData']['tx_grid']['language']['uid'] <= 0
        ) && (
            !isset($parameters['area']) ||
            !$parameters['area']['restricted'] &&
            $parameters['area']['assigned']
        );
    }

    /**
     * Returns LanguageService
     *
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }

    protected function getClipboard() : Clipboard
    {
        if ($this->clipboard === null) {
            $this->clipboard = GeneralUtility::makeInstance(Clipboard::class);
            $this->clipboard->initializeClipboard();
            $this->clipboard->lockToNormal();
            $this->clipboard->cleanCurrent();
            $this->clipboard->endClipboard();
        }

        return $this->clipboard;
    }

    /**
     * @param array $result
     * @param array $parameters
     * @return array
     */
    protected function getAttributes(array $result, array $parameters) : array
    {
        $defaults = array_merge([
            $result['customData']['tx_grid']['items']['config']['foreign_area_field'] => $parameters['area']['uid'],
            $result['customData']['tx_grid']['items']['vanillaTca']['ctrl']['languageField'] => $result['customData']['tx_grid']['language']['uid']
        ], $result['customData']['tx_grid']['items']['defaultValues']);

        return [
            'data' => [
                'uid' => $parameters['record']['uid'],
                'title' => $parameters['record']['title'],
                'target' => isset($parameters['item']) ? '-' . $parameters['item']['vanillaUid'] : $result['effectivePid'],
                'parameters' => [
                    'table' => $result['customData']['tx_grid']['items']['config']['foreign_table'],
                    'defaults' => TcaUtility::filterHiddenFields(
                        $result['customData']['tx_grid']['items']['vanillaTca']['columns'],
                        $defaults
                    ),
                    'overrides' => array_diff_key(
                        $defaults,
                        TcaUtility::filterHiddenFields(
                            $result['customData']['tx_grid']['items']['vanillaTca']['columns'],
                            $defaults
                        )
                    )
                ]
            ],
            'class' => 't3js-grid-paste' . ($parameters['mode'] ? '-' . $parameters['mode'] : '') . ' ' . $parameters['class'],
            'title' => $this->getLanguageService()->sL($parameters['title']),
            'icon' => 'actions-document-paste-into',
            'section' => $parameters['section'],
            'category' => 'ui'
        ];
    }
}

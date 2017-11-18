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

use TYPO3\CMS\Backend\Form\FormDataProviderInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Grid\Utility\TcaUtility;

/**
 * Add insert action for grid template areas of a grid container
 */
class CreateItemActionProvider implements FormDataProviderInterface
{
    /**
     * Add data
     *
     * @param array $result
     * @return array
     */
    public function addData(array $result)
    {
        foreach ($result['customData']['tx_grid']['template']['areas'] as &$area) {
            if (!$this->isAvailable($result, ['area' => $area])) {
                continue;
            }

            $area['actions']['insert'] = $this->getAttributes(
                $result,
                [
                    'area' => $area,
                    'section' => 'body'
                ]
            );

            foreach ($area['items'] as &$item) {
                $item['customData']['tx_grid']['actions']['append'] = $this->getAttributes(
                    $result,
                    [
                        'area' => $area,
                        'item' => $item,
                        'section' => 'after'
                    ]
                );
            }
        }

        return $result;
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
     * @param array $result
     * @return bool
     */
    protected function useWizard(array $result)
    {
        return (bool)$result['pageTsConfig']['tx_grid.'][$result['tableName'] . '.'][$result['customData']['tx_grid']['columnToProcess'] . '.']['disableContentPresetWizard'];
    }

    /**
     * @param array $result
     * @param array $parameters
     * @return bool
     */
    protected function isAvailable(array $result, array $parameters) : bool
    {
        return ($result['customData']['tx_grid']['localization']['mode'] !== 'strict' ||
            empty($result['customData']['tx_grid']['items']['children']) ||
            $result['customData']['tx_grid']['language']['uid'] <= 0) &&
            !$parameters['area']['restricted'] && $parameters['area']['assigned'];
    }

    /**
     * @param array $result
     * @param array $parameters
     * @return array
     */
    protected function getAttributes(array $result, array $parameters) : array
    {
        $attributes = null;

        if ($this->useWizard($result)) {
            $attributes = [
                'data' => [
                    'url' => BackendUtility::getModuleUrl(
                        'content_preset',
                        [
                            'action' => 'indexAction',
                            'containerTable' => $result['tableName'],
                            'containerField' => $result['customData']['tx_grid']['columnToProcess'],
                            'containerUid' => $result['customData']['tx_grid']['items']['config']['effectiveParentUid'],
                            'areaUid' => $parameters['area']['uid'],
                            'languageUid' => $result['customData']['tx_grid']['language']['uid'],
                            'returnUrl' => $result['returnUrl']
                        ] + (isset($parameters['item']['vanillaUid']) ? ['ancestorUid' => $parameters['item']['vanillaUid']] : [])
                    ),
                    'title' => $this->getLanguageService()->sL('LLL:EXT:lang/Resources/Private/Language/locallang_misc.xlf:newContentElement')
                ],
                'class' => 't3js-content-wizard-show'
            ];
        } else {
            $defaults = array_merge([
                $result['customData']['tx_grid']['items']['config']['foreign_area_field'] => $parameters['area']['uid'],
                $result['customData']['tx_grid']['items']['vanillaTca']['ctrl']['languageField'] => $result['customData']['tx_grid']['language']['uid']
            ], $result['customData']['tx_grid']['items']['defaultValues']);

            $attributes = [
                'url' => BackendUtility::getModuleUrl(
                    'record_edit',
                    [
                        'edit' => [
                            $result['customData']['tx_grid']['items']['config']['foreign_table'] => [
                                ($parameters['item'] ? -(int)$parameters['item']['vanillaUid'] : $result['effectivePid']) => 'new'
                            ]
                        ],
                        'defVals' => [
                            $result['customData']['tx_grid']['items']['config']['foreign_table'] => TcaUtility::filterHiddenFields(
                                $result['customData']['tx_grid']['items']['vanillaTca']['columns'],
                                $defaults
                            )
                        ],
                        'overrideVals' => [
                            $result['customData']['tx_grid']['items']['config']['foreign_table'] => array_diff_key(
                                $defaults,
                                TcaUtility::filterHiddenFields(
                                    $result['customData']['tx_grid']['items']['vanillaTca']['columns'],
                                    $defaults
                                )
                            )
                        ],
                        'returnUrl' => $result['returnUrl']
                    ]
                )
            ];
        }

        return $attributes + [
            'icon' => 'actions-add',
            'title' => $this->getLanguageService()->sL('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:newContentElement'),
            'section' => $parameters['section']
        ];
    }
}

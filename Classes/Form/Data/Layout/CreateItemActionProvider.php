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
            if ($this->isAvailable($result, ['area' => $area])) {
                $attributes = $this->getAttributes(
                    $result,
                    [
                        'area' => $area,
                        'section' => 'body'
                    ]
                );
                $area['actions']['insert'] = array_merge(
                    $attributes,
                    [
                        'url' => BackendUtility::getModuleUrl($attributes['url']['module'], $attributes['url']['parameters'])
                    ]
                );

                foreach ($area['items'] as &$item) {
                    $attributes = $this->getAttributes(
                        $result,
                        [
                            'area' => $area,
                            'item' => $item,
                            'section' => 'after'
                        ]
                    );
                    $item['customData']['tx_grid']['actions']['append'] = array_merge(
                        $attributes,
                        [
                            'url' => BackendUtility::getModuleUrl($attributes['url']['module'], $attributes['url']['parameters'])
                        ]
                    );
                }
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
        return $result['customData']['tx_grid']['localization']['mode'] !== 'strict' ||
            empty($result['customData']['tx_grid']['items']['children']) ||
            $result['customData']['tx_grid']['language']['uid'] <= 0;
    }

    /**
     * @param array $result
     * @param array $parameters
     * @return array
     */
    protected function getAttributes(array $result, array $parameters) : array
    {
        $url = null;

        if ($this->useWizard($result)) {
            $url = [
                'module' => 'content_preset',
                'parameters' => [
                    'action' => 'indexAction',
                    'containerTable' => $result['tableName'],
                    'containerField' => $result['customData']['tx_grid']['columnToProcess'],
                    'containerUid' => $result['vanillaUid'],
                    'areaUid' => $parameters['area']['uid'],
                    'languageUid' => $result['customData']['tx_grid']['language']['uid'],
                    'returnUrl' => $result['returnUrl']
                ]
            ];

            if ($parameters['item']) {
                $url['parameters']['ancestorUid'] = $parameters['item']['vanillaUid'];
            }
        } else {
            $defaultValues = array_merge([
                $result['processedTca']['ctrl']['EXT']['grid']['areaField'] => $parameters['area']['uid'],
                $result['customData']['tx_grid']['items']['vanillaTca']['ctrl']['languageField'] => $result['customData']['tx_grid']['language']['uid']
            ], $result['customData']['tx_grid']['items']['defaultValues']);

            $url = [
                'module' => 'record_edit',
                'parameters' => [
                    'edit' => [
                        $result['customData']['tx_grid']['items']['config']['foreign_table'] => [
                            ($parameters['item'] ? -(int)$parameters['item']['vanillaUid'] : $result['effectivePid']) => 'new'
                        ]
                    ],
                    'defVals' => [
                        $result['customData']['tx_grid']['items']['config']['foreign_table'] => TcaUtility::filterHiddenFields(
                            $result['customData']['tx_grid']['items']['vanillaTca']['columns'],
                            $defaultValues
                        )
                    ],
                    'overrideVals' => [
                        $result['customData']['tx_grid']['items']['config']['foreign_table'] => array_diff_key(
                            $defaultValues,
                            TcaUtility::filterHiddenFields(
                                $result['customData']['tx_grid']['items']['vanillaTca']['columns'],
                                $defaultValues
                            )
                        )
                    ],
                    'returnUrl' => $result['returnUrl']
                ]
            ];
        }

        return [
            'url' => $url,
            'icon' => 'actions-add',
            'title' => $this->getLanguageService()->sL('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:newContentElement'),
            'section' => $parameters['section']
        ];
    }
}

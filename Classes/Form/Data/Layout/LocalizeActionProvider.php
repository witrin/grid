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

use TYPO3\CMS\Backend\Configuration\TranslationConfigurationProvider;
use TYPO3\CMS\Backend\Form\FormDataProviderInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Grid\Utility\TcaUtility;

/**
 * Add localize actions for grid container
 */
class LocalizeActionProvider implements FormDataProviderInterface
{
    /**
     * Add data
     *
     * @param array $result
     * @return array
     */
    public function addData(array $result)
    {

        $translationInfo = GeneralUtility::makeInstance(TranslationConfigurationProvider::class)
            ->translationInfo($result['tableName'], $result['vanillaUid']);
        $disabledLanguages = array_flip(GeneralUtility::trimExplode(
            ',',
            (string)$result['pageTsConfig']['mod.']['SHARED.']['disableLanguages'],
            true
        ));

        if (isset($translationInfo['translations'])) {
            $languages = array_filter(
                array_diff_key(
                    $result['systemLanguageRows'],
                    $translationInfo['translations']
                ),
                function ($language) use ($disabledLanguages) {
                    return $this->getBackendUserAuthentication()->checkLanguageAccess($language['uid']) &&
                        !isset($disabledLanguages[$language['uid']]) &&
                        $language['uid'] > 0;
                }
            );

            $result['customData']['tx_grid']['actions']['localize'] = [];

            foreach ($languages as $language) {
                $attributes = $this->getAttributes($result, ['language' => $language]);
                $result['customData']['tx_grid']['actions']['localize'][] = array_merge(
                    $attributes,
                    [
                        'url' => BackendUtility::getModuleUrl($attributes['url']['module'], $attributes['url']['parameters'])
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
     * @param array $parameters
     * @return bool
     */
    protected function isAvailable(array $result, array $parameters) : bool
    {
        return !empty($result['processedTca']['ctrl']['languageField']);
    }

    /**
     * @param array $result
     * @param array $parameters
     * @return array
     */
    protected function getAttributes(array $result, array $parameters) : array
    {
        $defaultValues = [
            $result['processedTca']['ctrl']['languageField'] => $parameters['language']['uid']
        ];

        if (isset($result['processedTca']['ctrl']['transOrigPointerField'])) {
            $defaultValues[$result['processedTca']['ctrl']['transOrigPointerField']] = $result['vanillaUid'];
        }

        if (isset($result['processedTca']['ctrl']['translationSource'])) {
            $defaultValues[$result['processedTca']['ctrl']['translationSource']] = $result['vanillaUid'];
        }

        return [
            'url' => [
                'module' => 'record_edit',
                'parameters' => [
                    'edit' => [
                        $result['tableName'] => [
                            $result['effectivePid'] => 'new'
                        ]
                    ],
                    'defVals' => [
                        $result['tableName'] => TcaUtility::filterHiddenFields(
                            $result['processedTca']['columns'],
                            $defaultValues
                        )
                    ],
                    'overrideVals' => [
                        $result['tableName'] => array_diff_key(
                            $defaultValues,
                            TcaUtility::filterHiddenFields(
                                $result['processedTca']['columns'],
                                $defaultValues
                            )
                        )
                    ],
                    'returnUrl' => $result['returnUrl']
                ]
            ],
            'title' => $parameters['language']['title']
        ];
    }
}

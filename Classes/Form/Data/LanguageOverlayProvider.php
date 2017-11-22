<?php
declare(strict_types=1);
namespace TYPO3\CMS\Grid\Form\Data;

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
use TYPO3\CMS\Backend\Form\FormDataCompiler;
use TYPO3\CMS\Backend\Form\FormDataProviderInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Resolve additional language overlays for a grid container
 */
class LanguageOverlayProvider implements FormDataProviderInterface
{
    protected $translationProvider = null;

    protected $formDataCompiler = null;

    /**
     * Add form data to result array
     *
     * @param array $result Initialized result array
     * @return array Result filled with more data
     */
    public function addData(array $result)
    {
        $result['customData']['tx_grid']['overlays'] = [];

        if (!empty($result['processedTca']['ctrl']['languageField']) && !empty($result['processedTca']['ctrl']['transOrigPointerField'])) {
            $languages = $this->getAdditionalLanguages($result);

            foreach ($languages as $language) {
                $translationInfo = $this->getTranslationProvider()->translationInfo($result['tableName'], $result['vanillaUid'], $language['uid']);

                if (isset($translationInfo['translations'][$language['uid']])) {
                    $formDataCompilerInput = $this->getFormDataCompilerInput(
                        $result['tableName'],
                        (int)$translationInfo['translations'][$language['uid']]['uid'],
                        $result
                    );
                    $result['customData']['tx_grid']['overlays'][] = $this->getFormDataCompiler()->compile($formDataCompilerInput);
                }
            }
        }

        return $result;
    }

    protected function getAdditionalLanguages(array $result)
    {
        $result['customData']['tx_grid']['additionalLanguages'] = array_filter(
            (array)$result['customData']['tx_grid']['additionalLanguages'],
            function ($languageUid) use ($result) {
                return $languageUid > 0 && isset($result['systemLanguageRows'][$languageUid])
                    && $languageUid !== (int)$result['databaseRow'][$result['processedTca']['ctrl']['languageField']];
            }
        );

        return array_intersect_key(
            $result['systemLanguageRows'],
            array_flip($result['customData']['tx_grid']['additionalLanguages'])
        );
    }

    /**
     * @return FormDataCompiler
     */
    protected function getFormDataCompiler()
    {
        if ($this->formDataCompiler === null) {
            $this->formDataCompiler = GeneralUtility::makeInstance(FormDataCompiler::class, GeneralUtility::makeInstance(ContainerGroup::class));
        }

        return $this->formDataCompiler;
    }

    /**
     * @param string $tableName
     * @param int $recordUid
     * @param array $result
     * @return array
     */
    protected function getFormDataCompilerInput($tableName, $recordUid, $result)
    {
        return [
            'command' => 'edit',
            'tableName' => $tableName,
            'vanillaUid' => $recordUid,
            'columnsToProcess' => [$result['customData']['tx_grid']['columnToProcess']],
            'customData' => [
                'tx_grid' => [
                    'columnToProcess' => $result['customData']['tx_grid']['columnToProcess'],
                    'containerProviderList' => $result['customData']['tx_grid']['containerProviderList']
                ]
            ]
        ];
    }

    /**
     * @return TranslationConfigurationProvider
     */
    protected function getTranslationProvider()
    {
        if ($this->translationProvider === null) {
            $this->translationProvider = GeneralUtility::makeInstance(TranslationConfigurationProvider::class);
        }

        return $this->translationProvider;
    }
}

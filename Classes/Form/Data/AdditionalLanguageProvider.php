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
use TYPO3\CMS\Backend\Form\FormDataProviderInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Add additional languages
 */
class AdditionalLanguageProvider implements FormDataProviderInterface
{
    protected $translationProvider = null;

    /**
     * Add form data to result array
     *
     * @param array $result Initialized result array
     * @return array Result filled with more data
     */
    public function addData(array $result)
    {
        $result['customData']['tx_grid']['additionalLanguages'] = array_map('intval', (array)$result['customData']['tx_grid']['additionalLanguages']);

        if ((!empty($result['processedTca']['ctrl']['languageField'])
            && !empty($result['processedTca']['ctrl']['transOrigPointerField']))
            || $result['tableName'] == 'pages'
            && !empty($result['customData']['tx_grid']['additionalLanguages'])
        ) {
            foreach ($result['customData']['tx_grid']['additionalLanguages'] as $additionalLanguageUid) {
                if ($additionalLanguageUid > 0
                    && isset($result['systemLanguageRows'][$additionalLanguageUid])
                    && $additionalLanguageUid !== (int)$result['databaseRow'][$result['processedTca']['ctrl']['languageField']]
                ) {
                    $record = $this->getLanguageOverlayRecord(
                        $result['tableName'],
                        (int)$result['databaseRow'][$result['processedTca']['ctrl']['transOrigPointerField']],
                        $additionalLanguageUid
                    );

                    if (!empty($record)) {
                        $result['additionalLanguageRows'][$additionalLanguageUid] = $record;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @param string $tableName
     * @param int $uid
     * @param int $languageUid
     * @return array|null
     */
    protected function getLanguageOverlayRecord(string $tableName, int $uid, int $languageUid)
    {
        $record = null;
        $translationInfo = $this->getTranslationProvider()->translationInfo($tableName, $uid, $languageUid);

        if (!empty($translationInfo['translations'][$languageUid]['uid'])) {
            $record = $this->getRecordWorkspaceOverlay(
                $tableName,
                (int)$translationInfo['translations'][$languageUid]['uid']
            );
        }

        return $record;
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

    /**
     * @param string $tableName
     * @param int $uid
     * @return array
     */
    protected function getRecordWorkspaceOverlay(string $tableName, int $uid): array
    {
        return BackendUtility::getRecordWSOL($tableName, $uid) ?: [];
    }
}
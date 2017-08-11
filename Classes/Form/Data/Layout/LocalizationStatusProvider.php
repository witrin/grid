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

/**
 * Determines the localization status for the given grid areas
 */
class LocalizationStatusProvider implements FormDataProviderInterface
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
            $originalItems = [];
            $translatedItems = [];

            foreach ((array)$area['items'] as $item) {
                $languageUid = (int)$item['customData']['tx_grid']['languageUid'];

                if ($languageUid == 0) {
                    $originalItems[$item['databaseRow']['uid']] = &$item;
                } else {
                    $translatedItems[$languageUid][] = (int)$item['databaseRow'][$item['processedTca']['ctrl']['transOrigPointerField']][0];
                    $translatedItems[$languageUid][] = (int)$item['databaseRow'][$item['processedTca']['ctrl']['translationSource']];
                }
            }

            foreach ((array)$result['systemLanguageRows'] as $language) {
                if ($language['uid'] > 0) {
                    $area['localization']['status'][$language['uid']]['nonTranslated'] = array_diff_key(
                        $originalItems,
                        array_flip(isset($translatedItems[$language['uid']]) ? $translatedItems[$language['uid']] : [])
                    );
                }
            }
        }

        return $result;
    }
}

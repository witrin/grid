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
 * Determines the localization strategy for the given translations
 */
class LocalizationStrategyProvider implements FormDataProviderInterface
{
    /**
     * Add data
     *
     * @param array $result
     * @return array
     */
    public function addData(array $result)
    {
        foreach ($result['customData']['tx_grid']['items']['children'] as &$item) {
            $languageUid = (int)$item['customData']['tx_grid']['languageUid'];

            if ($languageUid > 0) {
                $defaultRecordUid = (int)$item['databaseRow'][$item['processedTca']['ctrl']['transOrigPointerField']][0];
                $strategy = &$result['customData']['tx_grid']['localization']['strategy'][$languageUid];

                if ($defaultRecordUid === 0) {
                    $strategy = empty($strategy) || $strategy == 'unbound' ? 'unbound' : 'mixed';
                } else if ($defaultRecordUid > 0) {
                    $strategy = empty($strategy) || $strategy == 'bound' ? 'bound' : 'mixed';
                }
            }
        }

        return $result;
    }
}

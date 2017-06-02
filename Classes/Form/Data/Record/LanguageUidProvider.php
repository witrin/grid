<?php
declare(strict_types=1);
namespace TYPO3\CMS\Grid\Form\Data\Record;

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
 * Add the language of a content element
 */
class LanguageUidProvider implements FormDataProviderInterface
{
    /**
     * Add data
     *
     * @param array $result
     * @return array
     */
    public function addData(array $result)
    {
        if (!empty($result['processedTca']['ctrl']['languageField'])) {
            $field = $result['processedTca']['ctrl']['languageField'];

            $result['customData']['tx_grid']['languageUid'] = (int)$result['databaseRow'][$field][0];
        } else {
            $result['customData']['tx_grid']['languageUid'] = -1;
        }

        return $result;
    }
}

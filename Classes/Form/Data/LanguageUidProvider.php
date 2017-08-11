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
        $result['customData']['tx_grid']['languageUid'] = $this->getLanguageUid($result);

        foreach ($result['customData']['tx_grid']['items']['children'] as &$item) {
            $item['customData']['tx_grid']['languageUid'] = $this->getLanguageUid($item);
        }

        return $result;
    }

    protected function getLanguageUid($data) {
        if (!empty($data['processedTca']['ctrl']['languageField'])) {
            return (int)$data['databaseRow'][$data['processedTca']['ctrl']['languageField']][0];
        } else {
            return  -1;
        }
    }
}

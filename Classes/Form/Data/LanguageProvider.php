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
 * Resolve the language for the grid container and its grid items
 */
class LanguageProvider implements FormDataProviderInterface
{
    /**
     * Add data
     *
     * @param array $result
     * @return array
     */
    public function addData(array $result)
    {
        $result['customData']['tx_grid']['language'] = $result['systemLanguageRows'][$this->getLanguageUid($result)];

        foreach ($result['customData']['tx_grid']['items']['children'] as &$item) {
            $item['customData']['tx_grid']['language'] = $item['systemLanguageRows'][$this->getLanguageUid($item)];
        }

        return $result;
    }

    /**
     * @param array $result
     * @return int
     */
    protected function getLanguageUid(array $result) {
        if (!empty($result['processedTca']['ctrl']['languageField'])) {
            return (int)$result['databaseRow'][$result['processedTca']['ctrl']['languageField']][0];
        } else {
            return 0;
        }
    }
}

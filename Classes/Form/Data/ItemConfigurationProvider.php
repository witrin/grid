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
use TYPO3\CMS\Grid\Utility\TcaUtility;

/**
 * Add the TCA of the items column
 */
class ItemConfigurationProvider implements FormDataProviderInterface
{
    /**
     * Add data
     *
     * @param array $result
     * @return array
     */
    public function addData(array $result)
    {
        $columnToProcess = $result['customData']['tx_grid']['columnToProcess'];

        if (
            !isset($result['processedTca']['columns'][$columnToProcess]['config'])
            || !is_array($result['processedTca']['columns'][$columnToProcess]['config'])
        ) {
            throw new \InvalidArgumentException(
                'Missing column configuration for ' . $columnToProcess . ' in TCA of ' . $result['tableName'],
                1465680013
            );
        }

        $result = $this->addEffectiveParentUid($result, $columnToProcess);
        $result = $this->addForeignMatchFields($result, $columnToProcess);

        $result['customData']['tx_grid']['items']['config'] = $result['processedTca']['columns'][$columnToProcess]['config'];

        return $result;
    }
    
    /**
     * Add the effective parent uid
     *
     * @param array $result Result array
     * @param string $fieldName Current handle field name
     * @return array Modified item array
     */
    protected function addEffectiveParentUid(array $result, $fieldName)
    {
        $tableName = $result['tableName'];
        $config = $result['processedTca']['columns'][$fieldName]['config'];

        if ($tableName === 'pages' && $config['foreign_field'] === 'pid') {
            $config['effectiveParentUid'] = $result['defaultLanguagePageRow']['uid'] ?? $result['vanillaUid'];
        } else {
            $config['effectiveParentUid'] = $result['vanillaUid'];
        }

        $result['processedTca']['columns'][$fieldName]['config'] = $config;

        return $result;
    }
    
    /**
     * Add the foreign match fields if necessary
     *
     * @param array $result Result array
     * @param string $fieldName Current handle field name
     * @return array Modified item array
     */
    protected function addForeignMatchFields(array $result, $fieldName)
    {
        $tableName = $result['tableName'];
        $childTableName = $result['processedTca']['columns'][$fieldName]['config']['foreign_table'];
        $config = $result['processedTca']['columns'][$fieldName]['config'];

        if (
            $tableName === 'pages' &&
            $config['foreign_field'] === 'pid' &&
            isset($GLOBALS['TCA'][$childTableName]['ctrl']['languageField'])
        ) {
            $config['foreign_match_fields'][$GLOBALS['TCA'][$childTableName]['ctrl']['languageField']]
                = TcaUtility::getLanguageUid($result['processedTca'], $result['databaseRow']);
        }

        $result['processedTca']['columns'][$fieldName]['config'] = $config;

        return $result;
    }
}

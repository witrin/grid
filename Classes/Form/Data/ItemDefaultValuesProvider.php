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
 * Add the default values for an entry in items
 */
class ItemDefaultValuesProvider implements FormDataProviderInterface
{
    /**
     * Add form data to result array
     *
     * @param array $result Initialized result array
     * @return array Result filled with more data
     */
    public function addData(array $result)
    {
        if (!empty($result['customData']['tx_grid']['items']['config'])) {
            $result['customData']['tx_grid']['items']['defaultValues'] = array_merge(
                $this->getDefaultValues(
                    $result['customData']['tx_grid']['items']['config'],
                    $result['customData']['tx_grid']['items']['config']['effectiveParentUid'],
                    $result['tableName']
                ),
                // allows overwriting through the caller
                array_filter((array)$result['customData']['tx_grid']['items']['defaultValues'])
            );
        }

        if (!empty($result['inlineParentConfig'])) {
            $result['customData']['tx_grid']['defaultValues'] = $this->getDefaultValues(
                $result['inlineParentConfig'],
                $result['inlineParentUid'],
                $result['inlineParentTableName']
            );
        }

        return $result;
    }


    /**
     * Get default values for a record
     *
     * @param array $parentConfig
     * @param $parentUid
     * @param $parentTableName
     * @return array
     */
    protected function getDefaultValues(array $parentConfig, $parentUid, $parentTableName) {
        return array_filter(
            array_merge([
                $parentConfig['foreign_field'] => $parentUid,
                $parentConfig['foreign_table_field'] => $parentTableName,
            ], (array)$parentConfig['foreign_match_fields']),
            function($key, $value) {
                return !empty($key) && !empty($value);
            },
            ARRAY_FILTER_USE_BOTH
        );
    }
}

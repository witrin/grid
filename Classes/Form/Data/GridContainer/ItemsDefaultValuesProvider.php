<?php
declare(strict_types=1);
namespace TYPO3\CMS\Grid\Form\Data\GridContainer;

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
class ItemsDefaultValuesProvider implements FormDataProviderInterface
{
    /**
     * Add data
     *
     * @param array $result
     * @return array
     */
    public function addData(array $result)
    {
        $config = $result['customData']['tx_grid']['itemsConfig'];

        $result['customData']['tx_grid']['itemsDefaultValues'] = array_filter(
            array_merge([
                $config['foreign_field'] => $result['vanillaUid'],
                $config['foreign_table_field'] => $result['tableName'],
            ], (array)$config['foreign_match_fields']),
            function($key, $value) {
                return !empty($key) && !empty($value);
            },
            ARRAY_FILTER_USE_BOTH
        );

        return $result;
    }
}

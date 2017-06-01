<?php
declare(strict_types=1);
namespace TYPO3\CMS\Wireframe\Form\Data\GridItem;

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
 * Add the grid area for the grid item
 */
class AreaUidProvider implements FormDataProviderInterface
{
    /**
     * Add data
     *
     * @param array $result
     * @return array
     */
    public function addData(array $result)
    {
        if (!isset($result['inlineParentConfig']['grid_area_field'])) {
            throw new \UnexpectedValueException(
                'Missing grid area field in TCA for table ' . $result['tableName'],
                1496151182
            );
        }

        $areaField = $result['inlineParentConfig']['grid_area_field'];

        if (!isset($GLOBALS['TCA'][$result['tableName']]['columns'][$areaField])) {
            throw new \UnexpectedValueException(
                'Grid area field ' . $areaField . ' does not exist in ' . $result['tableName'],
                1496151221
            );
        }

        $result['customData']['tx_grid']['areaUid'] = $result['databaseRow'][$areaField];

        return $result;
    }
}

<?php
declare(strict_types=1);
namespace TYPO3\CMS\Wireframe\Form\Data\GridContainer;

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
 * Add the items of a grid area
 */
class TemplateAreasItemsProvider implements FormDataProviderInterface
{
    /**
     * Add data
     *
     * @param array $result
     * @return array
     */
    public function addData(array $result)
    {
        if (empty($result['customData']['tx_grid']['itemsConfig']['grid_area_field'])) {
            throw new \UnexpectedValueException(
                'Missing grid_area_field in items configuration.',
                1496148555
            );
        }

        $areaField = $result['customData']['tx_grid']['itemsConfig']['grid_area_field'];

        if (!isset($result['customData']['tx_grid']['vanillaItemsTca']['columns'][$areaField])) {
            throw new \UnexpectedValueException(
                'Missing foreign field ' . $areaField . ' for grid areas in ' . $result['customData']['tx_grid']['itemsConfig']['foreign_table'],
                1496148578
            );
        }

        $areas = array_flip(array_column((array)$result['customData']['tx_grid']['template']['areas'], 'uid'));

        foreach ($result['customData']['tx_grid']['items'] as &$item) {
            $areaUid = is_array($item['databaseRow'][$areaField]) ? $item['databaseRow'][$areaField][0] : $item['databaseRow'][$areaField];
            if (isset($areas[$areaUid])) {
                $result['customData']['tx_grid']['template']['areas'][$areas[$areaUid]]['items'][] = &$item;
            }
        }

        return $result;
    }
}

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
 * Sort the areas of a grid template by there row and column
 */
class TemplateAreasSortingProvider implements FormDataProviderInterface
{
    /**
     * Add data
     *
     * @param array $result
     * @return array
     */
    public function addData(array $result)
    {
        $row = [];
        $column = [];

        foreach ((array)$result['customData']['tx_grid']['template']['areas'] as $key => &$area) {
            $row[$key] = $area['row']['start'];
            $column[$key] = $area['column']['start'];
        }

        array_multisort($row, SORT_ASC, $column, SORT_ASC, $result['customData']['tx_grid']['template']['areas']);

        return $result;
    }
}

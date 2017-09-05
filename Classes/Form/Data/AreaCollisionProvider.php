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
 * Check if grid areas collide with each other
 */
class AreaCollisionProvider implements FormDataProviderInterface
{
    /**
     * Add data
     *
     * @param array $result
     * @return array
     */
    public function addData(array $result)
    {
        $rows = $result['customData']['tx_grid']['template']['rows'] - 1;
        $columns = $result['customData']['tx_grid']['template']['columns'] - 1;
        $grid = array_fill(0, $rows, array_fill(0, $columns, null));

        foreach ((array)$result['customData']['tx_grid']['template']['areas'] as $area) {
            $n = $area['row']['end'] - 1;
            $m = $area['column']['end'] - 1;

            for ($i = $area['row']['start'] - 1; $i <= $n; $i++) {
                for ($j = $area['column']['start'] - 1; $j <= $m; $j++) {
                    if ($grid[$i][$j] !== null) {
                        throw new \UnexpectedValueException(
                            'Grid area ' . $area['uid'] . ' collides with area ' . $grid[$i][$j] . '.',
                            1438780511
                        );
                    } else {
                        $grid[$i][$j] = $area['uid'];
                    }
                }
            }
        }

        return $result;
    }
}

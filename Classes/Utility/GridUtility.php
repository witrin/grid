<?php
declare(strict_types=1);
namespace TYPO3\CMS\Wireframe\Utility;

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

/**
 * Grid Utility
 */
class GridUtility
{
    /**
     * Transform a grid into its table form
     *
     * @param array $template
     * @return array
     */
    static public function transformToTable(array &$template): array
    {
        $rows = $template['rows'] - 1;
        $columns = $template['columns'] - 1;
        $table = array_fill(0, $rows, array_fill(0, $columns, null));

        foreach ($template['areas'] as $area) {
            $min = [$area['row']['start'] - 1, $area['column']['start'] - 1];
            $max = [$area['row']['end'] - 1, $area['column']['end'] - 1];

            for ($i = $min[0]; $i <= $max[0]; $i++) {
                for ($j = $min[1]; $j <= $max[1]; $j++) {
                    $table[$i][$j] = ($i > $min[0] || $j > $min[1] ? '-' : $area);
                }
            }
        }

        return array_map(function($row) {
            return array_values(
                array_filter($row, function($cell) {
                    return $cell !== '-';
                })
            );
        }, $table);
    }
}
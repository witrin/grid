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
 * Calculate the dimensions of a grid template
 */
class TemplateDimensionsProvider implements FormDataProviderInterface
{
    /**
     * Add data
     *
     * @param array $result
     * @return array
     */
    public function addData(array $result)
    {
        $max = array_reduce(
            $result['customData']['tx_grid']['template']['areas'],
            function($max, &$area) {
                return [
                    max($max[0], $area['row']['end']),
                    max($max[1], $area['column']['end'])
                ];
            },
            [0,0]
        );

        $result['customData']['tx_grid']['template']['rows'] = $max[0];
        $result['customData']['tx_grid']['template']['columns'] = $max[1];

        return $result;
    }
}

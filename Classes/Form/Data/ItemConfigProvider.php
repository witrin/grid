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
 * Add the TCA of the items column
 */
class ItemConfigProvider implements FormDataProviderInterface
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

        $result['customData']['tx_grid']['items']['config'] = &$result['processedTca']['columns'][$columnToProcess]['config'];
        return $result;
    }
}

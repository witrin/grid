<?php
declare(strict_types=1);
namespace TYPO3\CMS\Grid\Form\Data\Layout;

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
 * Add visibility information for each content element of a content container
 */
class ItemVisibilityProvider implements FormDataProviderInterface
{
    /**
     * Add data
     *
     * @param array $result
     * @return array
     */
    public function addData(array $result)
    {
        foreach ($result['customData']['tx_grid']['items']['children'] as &$item) {
            if (!empty($item['processedTca']['ctrl']['enablecolumns']['disabled'])) {
                $field = $item['processedTca']['ctrl']['enablecolumns']['disabled'];

                if ($field && $item['databaseRow'][$field]) {
                    $item['customData']['tx_grid']['visibility'] = (bool)$item['databaseRow'][$field] ? 'visible' : 'hidden';
                }
            } else {
                $item['customData']['tx_grid']['visibility'] = 'visible';
            }
        }

        return $result;
    }
}

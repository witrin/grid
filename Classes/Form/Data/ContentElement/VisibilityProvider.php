<?php
declare(strict_types=1);
namespace TYPO3\CMS\Grid\Form\Data\ContentElement;

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
class VisibilityProvider implements FormDataProviderInterface
{
    /**
     * Add data
     *
     * @param array $result
     * @return array
     */
    public function addData(array $result)
    {
        if (!empty($result['processedTca']['ctrl']['enablecolumns']['disabled'])) {
            $field = $result['processedTca']['ctrl']['enablecolumns']['disabled'];

            if ($field && $result['databaseRow'][$field]) {
                $result['customData']['tx_grid']['visibility'] = (bool)$result['databaseRow'][$field] ? 'visible' : 'hidden';
            }
        } else {
            $result['customData']['tx_grid']['visibility'] = 'visible';
        }

        return $result;
    }
}

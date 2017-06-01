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
 * Add the vanilla TCA of the items table
 */
class ItemsTcaProvider implements FormDataProviderInterface
{
    /**
     * Add data
     *
     * @param array $result
     * @return array
     */
    public function addData(array $result)
    {
        $config = &$result['customData']['tx_grid']['itemsConfig'];

        if (empty($GLOBALS['TCA'][$config['foreign_table']]['ctrl']['sortby'])) {
            throw new \InvalidArgumentException(
                'Missing sorting field in TCA for table ' . $config['foreign_table'],
                1465681034
            );
        }

        $result['customData']['tx_grid']['vanillaItemsTca'] = $GLOBALS['TCA'][$config['foreign_table']];

        return $result;
    }
}

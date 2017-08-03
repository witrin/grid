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

use TYPO3\CMS\Backend\Form\FormDataCompiler;
use TYPO3\CMS\Backend\Form\FormDataProviderInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Grid\Form\Data\GridItemGroup;

/**
 * Process the items of a grid container and add a shortcut into the custom namespace
 */
class ItemRecordsProvider implements FormDataProviderInterface
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

        // shortcut for the items data
        $result['customData']['tx_grid']['items'] = &$result['processedTca']['columns'][$columnToProcess]['children'];

        foreach ($result['customData']['tx_grid']['items'] as $key => &$item) {
            $item['customData']['tx_grid'] = [
                'containerProviderList' => $result['customData']['tx_grid']['containerProviderList']
            ];
        }

        return $result;
    }
}

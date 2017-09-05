<?php
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
 * Add localization configuration for this page
 */
class LocalizationModeProvider implements FormDataProviderInterface
{
    /**
     * Add data
     *
     * @param array $result
     * @return array
     */
    public function addData(array $result)
    {
        $pageTsConfig = $result['pageTsConfig']['tx_grid.'][$result['tableName'] . '.'][$result['customData']['tx_grid']['columnToProcess'] . '.'];

        $result['customData']['tx_grid']['localization']['mode'] = $pageTsConfig['allowInconsistentLanguageHandling'] ? 'flexible' : 'strict';

        return $result;
    }
}

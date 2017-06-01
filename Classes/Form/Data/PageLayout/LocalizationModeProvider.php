<?php
namespace TYPO3\CMS\Wireframe\Form\Data\PageLayout;

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
use TYPO3\CMS\Backend\Utility\BackendUtility;

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
        $allowInconsistentLanguageHandling = BackendUtility::getModTSconfig(
            $result['vanillaUid'],
            'mod.web_layout.allowInconsistentLanguageHandling'
        );

        $result['customData']['tx_grid']['localization']['mode'] = $allowInconsistentLanguageHandling ? 'flexible' : 'strict';

        return $result;
    }
}

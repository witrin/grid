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
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Versioning\VersionState;

/**
 * Resolve view action URL for the grid container
 */
class ViewContainerActionProvider implements FormDataProviderInterface
{
    /**
     * Add data
     *
     * @param array $result
     * @return array
     * @todo Check condition when accessible
     */
    public function addData(array $result)
    {
        if ($this->isAvailable($result)) {
            $result['customData']['tx_grid']['actions']['view'] = $this->getAttributes($result);
        }

        return $result;
    }

    /**
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }

    /**
     * @param array $result
     * @param array $parameters
     * @return bool
     */
    protected function isAvailable(array $result, array $parameters = []) : bool
    {
        return !VersionState::cast($result['databaseRow']['t3ver_state'])->equals(VersionState::DELETE_PLACEHOLDER);
    }

    /**
     * @param array $result
     * @param array $parameters
     * @return array
     */
    protected function getAttributes(array $result, array $parameters = []) : array
    {
        return [
            'handler' => [
                'click' => BackendUtility::viewOnClick($result['vanillaUid'], '', BackendUtility::BEgetRootLine($result['vanillaUid']))
            ],
            'title' => $this->getLanguageService()->sL('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:view'),
            'icon' => 'actions-view',
            'priority' => 10
        ];
    }
}

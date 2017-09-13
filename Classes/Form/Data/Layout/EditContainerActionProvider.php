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
use TYPO3\CMS\Core\Type\Bitmask\Permission;

/**
 * Resolve edit action URL for the grid container
 */
class EditContainerActionProvider implements FormDataProviderInterface
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
            $attributes = $this->getAttributes($result);
            $result['customData']['tx_grid']['actions']['edit'] = array_merge(
                $attributes,
                [
                    'url' => BackendUtility::getModuleUrl($attributes['url']['module'], $attributes['url']['parameters']),
                ]
            );
        }

        return $result;
    }

    /**
     * @return BackendUserAuthentication
     */
    protected function getBackendUserAuthentication()
    {
        return $GLOBALS['BE_USER'];
    }

    /**
     * Returns LanguageService
     *
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
        return $this->getBackendUserAuthentication()->isAdmin() ||
            (($this->getBackendUserAuthentication()->calcPerms($result['databaseRow']) & Permission::PAGE_EDIT) === Permission::PAGE_EDIT &&
            !$result['databaseRow']['editlock']) &&
            $this->getBackendUserAuthentication()->checkLanguageAccess($result['customData']['tx_grid']['language']['uid']);
    }

    /**
     * @param array $result
     * @param array $parameters
     * @return array
     */
    protected function getAttributes(array $result, array $parameters = []) : array
    {
        return [
            'url' => [
                'module' => 'record_edit',
                'parameters' => [
                    'edit' => [
                        $result['tableName'] => [
                            $result['vanillaUid'] => 'edit'
                        ]
                    ],
                    'returnUrl' => $result['returnUrl']
                ]
            ],
            'title' => $this->getLanguageService()->sL('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:edit'),
            'icon' => 'actions-open',
            'priority' => 20
        ];
    }
}

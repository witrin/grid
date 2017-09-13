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

/**
 * Add action URLs for the content element
 */
class EditItemActionProvider implements FormDataProviderInterface
{
    /**
     * Add data
     *
     * @param array $result
     * @return array
     * @todo PageTsConfig
     */
    public function addData(array $result)
    {
        foreach ($result['customData']['tx_grid']['items']['children'] as &$item) {
            if ($this->isAvailable($result, ['item' => $item])) {
                $attributes = $this->getAttributes($result, ['item' => $item]);
                $item['customData']['tx_grid']['actions']['edit'] = array_merge(
                    $attributes,
                    [
                        'url' => BackendUtility::getModuleUrl($attributes['url']['module'], $attributes['url']['parameters']) . '#element-' . $item['tableName'] . '-' . $item['vanillaUid']
                    ]
                );
            }
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
    protected function isAvailable(array $result, array $parameters) : bool
    {
        return true;
    }

    /**
     * @param array $result
     * @param array $parameters
     * @return array
     */
    protected function getAttributes(array $result, array $parameters) : array
    {
        return [
            'url' => [
                'module' => 'record_edit',
                'parameters' => [
                    'edit' => [
                        $parameters['item']['tableName'] => [
                            $parameters['item']['vanillaUid'] => 'edit'
                        ]
                    ],
                    'returnUrl' => $parameters['item']['returnUrl']
                ]
            ],
            'title' => $this->getLanguageService()->sL('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:edit'),
            'icon' => 'actions-open',
            'section' => 'header',
            'priority' => 10
        ];
    }
}

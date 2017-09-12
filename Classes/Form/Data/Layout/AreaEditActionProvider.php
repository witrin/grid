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
class AreaEditActionProvider implements FormDataProviderInterface
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
        foreach ($result['customData']['tx_grid']['template']['areas'] as &$area) {
            if ($this->isAvailable($result, ['area' => $area])) {
                $attributes = $this->getAttributes($result, ['area' => $area]);
                $area['actions']['edit'] = array_merge(
                    $attributes,
                    [
                        'url' => BackendUtility::getModuleUrl($attributes['url']['module'], $attributes['url']['parameters'])
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
    protected function isAvailable(array $result, array $parameters) : bool
    {
        return !empty($parameters['area']['items']) &&
            $this->getBackendUserAuthentication()->checkLanguageAccess($result['customData']['tx_grid']['language']['uid']);
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
                        $result['customData']['tx_grid']['items']['config']['foreign_table'] => [
                            implode(
                                ',',
                                array_map(
                                    function($item) {
                                        return $item['vanillaUid'];
                                    },
                                    $parameters['area']['items']
                                )
                            ) => 'edit'
                        ]
                    ],
                    'recTitle' => $result['recordTitle'],
                    'returnUrl' => $result['returnUrl']
                ]
            ],
            'title' => $this->getLanguageService()->sL('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:editColumn'),
            'icon' => 'actions-document-open',
            'section' => 'header',
            'priority' => 20
        ];
    }
}

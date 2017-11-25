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
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

/**
 * Add localize action for grid area overlays
 *
 * @todo Support localize action without overlays
 */
class LocalizeAreaActionProvider implements FormDataProviderInterface
{
    /**
     * Add data
     *
     * @param array $result
     * @return array
     */
    public function addData(array $result)
    {
        $areas = array_column($result['customData']['tx_grid']['template']['areas'], null, 'uid');

        foreach ($result['customData']['tx_grid']['overlays'] as &$overlay) {
            $translated = array_column(
                array_column(
                    (array)$overlay['customData']['tx_grid']['items']['children'],
                    'defaultLanguageRow'
                ),
                'uid'
            );

            foreach ($overlay['customData']['tx_grid']['template']['areas'] as &$area) {
                if (!$this->isAvailable($result, ['area' => $areas[$area['uid']], 'translated' => $translated])) {
                    continue;
                }

                $area['actions']['localize'] = $this->getAttributes($overlay, ['overlay' => $areas[$area['uid']], 'area' => $area]);
            }
        }

        return $result;
    }

    /**
     * @param array $result
     * @param array $parameters
     * @return bool
     */
    protected function isAvailable(array $result, array $parameters) : bool
    {
        return $result['customData']['tx_grid']['language']['uid'] <= 0 &&
            !empty($result['customData']['tx_grid']['overlays']) &&
            !empty(array_diff(array_column((array)$parameters['area']['items'], 'vanillaUid'), $parameters['translated']));
    }

    /**
     * @param array $result
     * @param array $parameters
     * @return array
     */
    protected function getAttributes(array $result, array $parameters) : array
    {
        return [
            'data' => [
                'has-elements' => (int)!empty($parameters['overlay']['items']),
                'allow-copy' => (int)($result['customData']['tx_grid']['localization']['strategy'] === 'unbound' ||
                    $result['customData']['tx_grid']['localization']['strategy'] === null),
                'allow-translate' => (int)($result['customData']['tx_grid']['localization']['strategy'] === 'bound' ||
                    $result['customData']['tx_grid']['localization']['strategy'] === null),
                'container-table' => $result['tableName'],
                'container-field' => $result['customData']['tx_grid']['columnToProcess'],
                'area-uid' => $parameters['area']['uid'],
                'language-uid' => $result['customData']['tx_grid']['language']['uid'],
                'language-title' => $result['customData']['tx_grid']['language']['title'],
                'area-title' => $parameters['area']['title'],
                'container-uid' => $result['customData']['tx_grid']['items']['config']['effectiveParentUid']
            ],
            'icon' => 'actions-localize',
            'title' => $this->getLanguageService()->sL('LLL:EXT:lang/Resources/Private/Language/locallang_misc.xlf:localize'),
            'section' => 'header',
            'category' => 'ui',
            'class' => 't3js-localize',
            'priority' => 10
        ];
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
}

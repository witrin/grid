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
 * Add action URLs for the content element
 */
class TemplateAreasLocalizeActionProvider implements FormDataProviderInterface
{
    /**
     * Add data
     *
     * @param array $result
     * @return array
     */
    public function addData(array $result)
    {
        // @todo PageTsConfig
        $authentication = $this->getBackendUserAuthentication();
        $customData = &$result['customData']['tx_grid'];

        if (
            $authentication->recordEditAccessInternals($result['tableName'], $result['databaseRow']) &&
            $authentication->doesUserHaveAccess($result['parentPageRow'], Permission::CONTENT_EDIT)
        ) {
            foreach ($customData['template']['areas'] as &$area) {
                $area['actions']['localize'] = $this->getAction($result, $area, $customData['languageUid']);

                if (is_array($area['overlays'])) {
                    // @todo not sure if something like this might be not better part of a separate data provider
                    foreach ($area['overlays'] as &$overlay) {
                        $overlay['actions']['localize'] = $this->getAction($result, $area, $overlay['languageUid']);
                    }
                }
            }

        }

        return $result;
    }

    /**
     * @param array $result
     * @param $area
     * @param $languageUid
     * @return array
     */
    protected function getAction(array &$result, array $area, $languageUid) : array
    {
        $customData = &$result['customData']['tx_grid'];

        if ($languageUid > 0 && count($area['localization']['status'][$languageUid]['nonTranslated']) > 0) {
            $action = [
                'url' => null,
                'data' => [
                    'has-elements' => (int)(count(array_filter((array)$area['items'],
                            function ($item) use ($languageUid) {
                                return $item['customData']['tx_grid']['languageUid'] === $languageUid;
                            })) > 0),
                    'allow-copy' => (int)(isset($customData['localization']['strategy'][$languageUid])
                        && $customData['localization']['strategy'][$languageUid] == 'unbound'
                        || empty($customData['localization']['strategy'][$languageUid])),
                    'allow-translate' => isset($customData['localization']['strategy'][$languageUid])
                        && $customData['localization']['strategy'][$languageUid] == 'bound'
                        || empty($customData['localization']['strategy'][$languageUid]),
                    'container-table' => $result['tableName'],
                    'relationship-column' => $customData['columnToProcess'],
                    'area-uid' => $area['uid'],
                    'language-uid' => $languageUid,
                    'area-title' => 'Foo',
                    'container-uid' => $result['vanillaUid']
                ]
            ];
        } else {
            $action = [];
        }

        return $action;
    }

    /**
     * @return BackendUserAuthentication
     */
    protected function getBackendUserAuthentication()
    {
        return $GLOBALS['BE_USER'];
    }
}

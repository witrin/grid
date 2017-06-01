<?php
declare(strict_types=1);
namespace TYPO3\CMS\Wireframe\Form\Data\LayoutContainer;

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
use TYPO3\CMS\Wireframe\Utility\TcaUtility;

/**
 * Add insert action URLs for the grid template areas of a grid container
 */
class TemplateAreasInsertActionProvider implements FormDataProviderInterface
{
    /**
     * Add data
     *
     * @param array $result
     * @return array
     */
    public function addData(array $result)
    {
        $authentication = $this->getBackendUserAuthentication();
        $languageUid = $result['customData']['tx_grid']['languageUid'];

        if (
            $authentication->recordEditAccessInternals($result['tableName'], $result['databaseRow']) &&
            $authentication->doesUserHaveAccess($result['parentPageRow'], Permission::CONTENT_EDIT)
        ) {
            foreach ($result['customData']['tx_grid']['template']['areas'] as &$area) {
                $area['actions']['insert'] = $this->getAction($result, $area['uid'], $languageUid);

                if (is_array($area['overlays'])) {
                    // @todo not sure if something like this might be not better part of a separate data provider
                    foreach ($area['overlays'] as &$overlay) {
                        $overlay['actions']['insert'] = $this->getAction($result, $overlay['uid'],
                            $overlay['languageUid']);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @param array $result
     * @param $areaUid
     * @param $languageUid
     * @return array
     */
    protected function getAction(array &$result, $areaUid, $languageUid) : array
    {
        $pageTsConfig = $result['pageTsConfig']['tx_grid.'][$result['tableName'] . '.'][$result['customData']['tx_grid']['columnToProcess'] . '.'];

        if (isset($pageTsConfig['disableContentTypeWizard']) && (bool)$pageTsConfig['disableContentTypeWizard']) {
            $defaultValues = array_merge([
                $result['processedTca']['ctrl']['EXT']['grid']['areaField'] => $areaUid,
                $result['processedTca']['ctrl']['languageField'] => $languageUid
            ], $result['customData']['tx_grid']['itemsDefaultValues']);

            $action = [
                'url' => BackendUtility::getModuleUrl(
                    'record_edit',
                    [
                        'edit' => [
                            $result['tableName'] => [
                                $result['inlineParentUid'] => 'new'
                            ]
                        ],
                        'defVals' => [
                            $result['tableName'] => TcaUtility::filterHiddenFields(
                                $result['processedTca']['columns'],
                                $defaultValues
                            )
                        ],
                        'overrideVals' => [
                            $result['tableName'] => array_diff_key(
                                $defaultValues,
                                TcaUtility::filterHiddenFields(
                                    $result['processedTca']['columns'],
                                    $defaultValues
                                )
                            )
                        ],
                        'returnUrl' => $result['returnUrl']
                    ]
                )
            ];
        } else {
            $action = [
                'url' => BackendUtility::getModuleUrl(
                    'content_element',
                    [
                        'action' => 'indexAction',
                        'containerTable' => $result['tableName'],
                        'containerField' => $result['customData']['tx_grid']['columnToProcess'],
                        'containerUid' => $result['vanillaUid'],
                        'areaUid' => $areaUid,
                        'languageUid' => $languageUid,
                        'returnUrl' => $result['returnUrl']
                    ]
                )
            ];
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

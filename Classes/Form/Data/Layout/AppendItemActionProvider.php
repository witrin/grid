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
use TYPO3\CMS\Grid\Utility\TcaUtility;

/**
 * Add action URLs for the
 */
class AppendItemActionProvider implements FormDataProviderInterface
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
        $pageTsConfig = $result['pageTsConfig']['tx_grid.'][$result['tableName'] . '.'][$result['customData']['tx_grid']['columnToProcess'] . '.'];

        foreach ($result['customData']['tx_grid']['items'] as $key => &$item) {
            if (
                $authentication->recordEditAccessInternals($item['tableName'], $item['databaseRow']) &&
                $authentication->doesUserHaveAccess($item['parentPageRow'], Permission::CONTENT_EDIT)
            ) {
                if (isset($pageTsConfig['disableContentPresetWizard']) && (bool)$pageTsConfig['disableContentPresetWizard']) {
                    $defaultValues = array_merge([
                        $item['processedTca']['ctrl']['EXT']['grid']['areaField'] => $item['customData']['tx_grid']['areaUid'],
                        $item['processedTca']['ctrl']['languageField'] => $item['customData']['tx_grid']['languageUid']
                    ], $result['customData']['tx_grid']['itemsDefaultValues']);

                    $item['customData']['tx_grid']['actions']['append'] = BackendUtility::getModuleUrl(
                        'record_edit',
                        [
                            'edit' => [
                                $item['tableName'] => [
                                    -(int)$item['vanillaUid'] => 'new'
                                ]
                            ],
                            'defVals' => [
                                $item['tableName'] => TcaUtility::filterHiddenFields($result['processedTca']['columns'],
                                    $defaultValues)
                            ],
                            'overrideVals' => [
                                $item['tableName'] => array_diff_key($defaultValues,
                                    TcaUtility::filterHiddenFields($item['processedTca']['columns'], $defaultValues))
                            ],
                            'returnUrl' => $item['returnUrl']
                        ]
                    );
                } else {
                    $item['customData']['tx_grid']['actions']['append'] = BackendUtility::getModuleUrl(
                        'content_element',
                        [
                            'action' => 'indexAction',
                            'containerTable' => $item['inlineParentTableName'],
                            'containerField' => $item['inlineParentFieldName'],
                            'containerUid' => $item['inlineParentUid'],
                            'columnPosition' => $item['customData']['tx_grid']['areaUid'],
                            'ancestorUid' => $item['vanillaUid'],
                            'languageUid' => $item['customData']['tx_grid']['languageUid'],
                            'returnUrl' => $item['returnUrl']
                        ]
                    );
                }
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
}

<?php
declare(strict_types=1);
namespace TYPO3\CMS\Grid\Form\Data\PageContent;

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
class AppendActionProvider implements FormDataProviderInterface
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
        $pageTsConfig = $result['pageTsConfig']['mod.']['web_layout.'];

        if (
            $authentication->recordEditAccessInternals($result['tableName'], $result['databaseRow']) &&
            $authentication->doesUserHaveAccess($result['parentPageRow'], Permission::CONTENT_EDIT)
        ) {
            $defaultValues = array_merge([
                $result['inlineParentFieldName'] => $result['customData']['tx_grid']['areaUid'],
                $result['processedTca']['ctrl']['languageField'] => $result['customData']['tx_grid']['languageUid']
            ], $result['customData']['tx_grid']['container']['itemsDefaultValues']);

            if (isset($pageTsConfig['disableNewContentElementWizard']) && (bool)$pageTsConfig['disableNewContentElementWizard']) {
                $result['customData']['tx_grid']['actions']['append'] = BackendUtility::getModuleUrl(
                    'record_edit',
                    [
                        'edit' => [
                            $result['tableName'] => [
                                -(int)$result['vanillaUid'] => 'new'
                            ]
                        ],
                        'defVals' => [
                            $result['tableName'] => TcaUtility::filterHiddenFields($result['processedTca']['columns'], $defaultValues)
                        ],
                        'overrideVals' => [
                            $result['tableName'] => array_diff_key($defaultValues, TcaUtility::filterHiddenFields($result['processedTca']['columns'], $defaultValues))
                        ],
                        'returnUrl' => $result['returnUrl']
                    ]
                );
            } else {
                $result['customData']['tx_grid']['actions']['append'] = BackendUtility::getModuleUrl(
                    'content_element',
                    [
                        'action' => 'createAction',
                        'containerTable' => $result['inlineParentTableName'],
                        'containerField' => $result['inlineParentFieldName'],
                        'containerUid' => $result['inlineParentUid'],
                        'columnPosition' => $result['customData']['tx_grid']['areaUid'],
                        'ancestorUid' => $result['vanillaUid'],
                        'languageUid' => $result['customData']['tx_grid']['languageUid'],
                        'returnUrl' => $result['returnUrl']
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
}

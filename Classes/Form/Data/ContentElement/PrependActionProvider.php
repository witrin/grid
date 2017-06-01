<?php
declare(strict_types=1);
namespace TYPO3\CMS\Wireframe\Form\Data\ContentElement;

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
 * Add action URLs for the
 */
class PrependActionProvider implements FormDataProviderInterface
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

        if (
            $authentication->recordEditAccessInternals($result['tableName'], $result['databaseRow']) &&
            $authentication->doesUserHaveAccess($result['parentPageRow'], Permission::CONTENT_EDIT)
        ) {
            $values = array_merge([
                $result['inlineParentFieldName'] => $result['customData']['tx_grid']['areaUid'],
                $result['processedTca']['ctrl']['languageField'] => $result['customData']['tx_grid']['languageUid']
            ], (array)$result['customData']['tx_grid']['container']['itemsDefaultValues']);

            $result['customData']['tx_grid']['actions']['prependWithWizard'] = BackendUtility::getModuleUrl(
                'content_element',
                [
                    'action' => 'indexAction',
                    'containerTable' => $result['inlineParentTableName'],
                    'containerField' => $result['inlineParentFieldName'],
                    'containerUid' => $result['inlineParentUid'],
                    'columnPosition' => $result['customData']['tx_grid']['areaUid'],
                    'languageUid' => $result['customData']['tx_grid']['languageUid'],
                    'returnUrl' => $result['returnUrl']
                ]
            );

            $result['customData']['tx_grid']['actions']['prependWithForm'] = BackendUtility::getModuleUrl(
                'record_edit',
                [
                    'edit' => [
                        $result['tableName'] => [
                            $result['inlineParentUid'] => 'new'
                        ]
                    ],
                    'defVals' => [
                        $result['tableName'] => TcaUtility::filterHiddenFields($result['processedTca']['columns'], $values)
                    ],
                    'overrideVals' => [
                        $result['tableName'] => array_diff_key($values, TcaUtility::filterHiddenFields($result['processedTca']['columns'], $values))
                    ],
                    'returnUrl' => $this->data['returnUrl']
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
}

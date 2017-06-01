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

/**
 * Add action URLs for the content element
 */
class DeleteActionProvider implements FormDataProviderInterface
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
            $result['customData']['tx_grid']['actions']['delete'] = BackendUtility::getLinkToDataHandlerAction(
                '&cmd[' . $result['tableName'] . '][' . $result['vanillaUid'] . '][delete]=1'
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

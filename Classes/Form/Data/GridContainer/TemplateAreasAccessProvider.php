<?php
declare(strict_types=1);
namespace TYPO3\CMS\Grid\Form\Data\GridContainer;

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
use TYPO3\CMS\Core\Type\Bitmask\Permission;

/**
 * Add accessibility information about backend layout columns
 */
class TemplateAreasAccessProvider implements FormDataProviderInterface
{
    /**
     * Add data
     *
     * @param array $result
     * @return array
     */
    public function addData(array $result)
    {
        $permissions = $this->getBackendUserAuthentication()->calcPerms(
            $result['tableName'] === 'pages' ? $result['databaseRow'] : $result['parentPageRow']
        );
        $locked = !$this->getBackendUserAuthentication()->isAdmin() &&
            (($permissions & Permission::CONTENT_EDIT) !== Permission::CONTENT_EDIT ||
                isset($result['databaseRow']['editlock']) && $result['databaseRow']['editlock']);

        foreach ($result['customData']['tx_grid']['template']['areas'] as &$area) {
            $area['locked'] = $locked;
            $area['restricted'] = !$area['assigned'] || $area['locked'];
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

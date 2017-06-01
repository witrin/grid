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
class TemplateAreasOverlaysProvider implements FormDataProviderInterface
{
    /**
     * Add data
     *
     * @param array $result
     * @return array
     */
    public function addData(array $result)
    {
        foreach ($result['customData']['tx_grid']['template']['areas'] as &$area) {
            foreach ($result['systemLanguageRows'] as &$language) {
                if ($language['uid'] > 0) {
                    $area['overlays'][] = [
                        'languageUid' => $language['uid']
                    ];
                }
            }
        }

        return $result;
    }
}

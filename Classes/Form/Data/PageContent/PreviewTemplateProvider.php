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

/**
 * Add Fluid preview template file name for a content element from PageTsConfig
 */
class PreviewTemplateProvider implements FormDataProviderInterface
{
    /**
     * Add data
     *
     * @param array $result
     * @return array
     */
    public function addData(array $result)
    {
        $typeField = $result['processedTca']['ctrl']['type'];
        $typeValue = empty($result['databaseRow'][$typeField]) ? 'default' : $result['databaseRow'][$typeField];
        // @todo check when is this an array and when it's not
        $typeValue = is_array($typeValue) ? $typeValue[0] : $typeValue;
        $pageTsConfig = $result['pageTsConfig']['mod.']['web_layout.'];

        if (!empty($pageTsConfig[$result['tableName'] . '.']['preview.'][$typeValue])) {
            $result['customData']['tx_grid']['previewTemplate'] = $pageTsConfig[$result['tableName'] . '.']['preview.'][$typeValue];
        }

        return $result;
    }
}

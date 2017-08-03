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

/**
 * Add Fluid preview template file name for a content element from PageTsConfig
 */
class ItemPreviewTemplateProvider implements FormDataProviderInterface
{
    /**
     * Add data
     *
     * @param array $result
     * @return array
     */
    public function addData(array $result)
    {
        foreach ($result['customData']['tx_grid']['items'] as $key => &$item) {
            $typeField = $item['processedTca']['ctrl']['type'];
            $typeValue = empty($item['databaseRow'][$typeField]) ? 'default' : $item['databaseRow'][$typeField];
            // @todo check when is this an array and when it's not
            $typeValue = is_array($typeValue) ? $typeValue[0] : $typeValue;
            $pageTsConfig = $item['pageTsConfig']['tx_grid.'][$item['inlineParentTableName'] . '.'][$item['inlineParentFieldName'] . '.'];

            if (!empty($pageTsConfig['preview.'][$typeValue])) {
                $item['customData']['tx_grid']['previewTemplate'] = $pageTsConfig['preview.'][$typeValue];
            }
        }

        return $result;
    }
}

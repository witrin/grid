<?php
declare(strict_types=1);
namespace TYPO3\CMS\Grid\Form\Data;

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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;

/**
 * Add grid template configuration from PageTsConfig `tx_grid.<table>.<column>.template`
 */
class TemplateDefinitionProvider implements FormDataProviderInterface
{
    /**
     * Add data
     *
     * @param array $result
     * @return array
     */
    public function addData(array $result)
    {
        $tableName = $result['tableName'];
        $columnToProcess = $result['customData']['tx_grid']['columnToProcess'];
        $template = (array)$result['pageTsConfig']['tx_grid.'][$tableName . '.'][$columnToProcess . '.']['template.'];

        if (empty($template)) {
            throw new \InvalidArgumentException(
                'Missing grid template configuration for column  ' . $columnToProcess . ' in table ' . $tableName,
                1494090766
            );
        }

        $result['customData']['tx_grid']['template'] = GeneralUtility::removeDotsFromTS($template);

        foreach ($result['customData']['tx_grid']['template']['areas'] as &$area) {
            $area['title'] = GeneralUtility::isFirstPartOfStr($area['title'], 'LLL:') ?
                $this->getLanguageService()->sL($area['title']) : $area['title'];

            $area['title'] = BackendUtility::getProcessedValue(
                $result['customData']['tx_grid']['items']['config']['foreign_table'],
                $result['customData']['tx_grid']['items']['vanillaTca']['ctrl']['EXT']['tx_grid']['areaField'],
                $area['uid']
            ) ?? $area['title'];

            // @todo do we need BackendUtility::getProcessedValue() here to check this?
            $area['assigned'] = isset($area['uid']);
            $area['uid'] = $area['uid'] ?? StringUtility::getUniqueId();
            $area['column']['start'] = max(1, $area['column']['start']);
            $area['row']['start'] = max(1, $area['row']['start']);
            $area['column']['end'] = max($area['column']['end'], $area['column']['start']);
            $area['row']['end'] = max($area['row']['end'], $area['row']['start']);
        }

        $result['customData']['tx_grid']['template']['areas'] = array_values($result['customData']['tx_grid']['template']['areas']);

        return $result;
    }

    /**
     * Returns LanguageService
     *
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }
}

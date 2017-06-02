<?php
declare(strict_types=1);
namespace TYPO3\CMS\Grid\Form\Data\PageLayout;

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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Process content types configuration in PageTsConfig `mod.wizards.newContentElement.wizardItems`
 */
class ItemPresetsProvider implements FormDataProviderInterface
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
        $groups = (array)$result['pageTsConfig']['mod.']['wizards.']['newContentElement.']['wizardItems.'];

        $this->processHook($groups);

        // map configuration into common format
        array_walk($groups, function(&$group) {
            array_walk($group['elements.'], function(&$element) {
                // map config entry `tt_content_defValues` to `defaultValues`
                if (isset($element['tt_content_defValues.'])) {
                    $element['defaultValues.'] = $element['tt_content_defValues.'];
                    unset($element['tt_content_defValues.']);
                }
                // extract additional default values from parameter string (old style)
                if ($element['params']) {
                    $parameters = GeneralUtility::explodeUrl2Array($element['params'], true);

                    $element['defaultValues.'] = array_merge(
                        (array)$element['defaultValues'],
                        (array)$parameters['defVals']['tt_content']
                    );

                    unset($element['params']);
                }
            });
        });

        $result['pageTsConfig']['tx_grid.'][$tableName . '.'][$columnToProcess . '.']['presets.'] = $groups;

        return $result;
    }

    /**
     * @param array $groups
     */
    protected function processHook(array &$groups) {
        $elements = [];

        foreach ((array)$GLOBALS['TBE_MODULES_EXT']['xMOD_db_new_content_el']['addElClasses'] as $class => $path) {
            require_once $path;
            $elements = GeneralUtility::makeInstance($class)->proc($elements);
        }

        foreach ((array)$elements as $key => $element) {
            preg_match('/^[a-zA-Z0-9]+_/', $key, $group);
            $group = $group[0] ? substr($group[0], 0, -1) . '.' : $key;
            $groups[$group]['elements.'][substr($key, strlen($group)) . '.'] = $element;
        }
    }
}

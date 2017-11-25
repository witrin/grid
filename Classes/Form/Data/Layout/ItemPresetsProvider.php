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
use TYPO3\CMS\Core\Service\DependencyOrderingService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Grid\Utility\TcaUtility;

/**
 * Add content type groups from PageTsConfig `tx_grid.<table>.<column>.presets`
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
        $groups = (array)$result['pageTsConfig']['tx_grid.'][$tableName . '.'][$columnToProcess . '.']['presets.'];

        foreach ($groups as $key => &$group) {
            $this->prepareDependencyOrdering($group, 'before');
            $this->prepareDependencyOrdering($group, 'after');
        }

        $groups = GeneralUtility::makeInstance(DependencyOrderingService::class)->orderByDependencies($groups);

        foreach ($groups as $key => $group) {
            $key = rtrim($key, '.');
            $group = (array)$group;

            $this->processLabels(['header'], $group);

            $result['customData']['tx_grid']['itemPresets'][$key] = [
                'header' => $group['header'],
                'elements' => $this->processGroup(
                    $group,
                    $result['customData']['tx_grid']['items']['config']['foreign_table'],
                    $result['customData']['tx_grid']['items']['defaultValues'],
                    $result['pageTsConfig']
                )
            ];
        }

        // filter empty groups from presets
        $result['customData']['tx_grid']['itemPresets'] = array_filter(
            (array)$result['customData']['tx_grid']['itemPresets'],
            function ($group) {
                return !empty($group['elements']);
            }
        );

        return $result;
    }

    /**
     * @param array $group
     * @param string $table
     * @param array $pageTsConfig
     * @return array
     */
    protected function processGroup(array $group, $table, array $defaults, array $pageTsConfig)
    {
        $filter = $group['show'] === '*' ? true : GeneralUtility::trimExplode(',', $group['show'], true);
        $result = [];

        foreach ((array)$group['elements.'] as $key => &$element) {
            $key = rtrim($key, '.');
            $element = (array)$element;

            if ($filter || in_array($key, $filter)) {
                $this->processLabels(['title', 'description'], $element);

                if ($this->isValidElement($element, $table, $pageTsConfig)) {
                    $element['key'] = $key;

                    $parameters = array_merge((array)$element['defaults.'], $defaults);

                    $element['parameters'] = [
                        'table' => $table,
                        'defaults' => TcaUtility::filterHiddenFields($GLOBALS['TCA'][$table]['columns'], $parameters),
                        'overrides' => array_diff_key($parameters, TcaUtility::filterHiddenFields($GLOBALS['TCA'][$table]['columns'], $parameters)),
                    ];

                    $result[$key] = $element;
                }
            }
        }

        return $result;
    }

    /**
     * @param array $element
     * @param string $table
     * @param array $pageTsConfig
     * @return bool
     */
    protected function isValidElement(array $element, $table, array $pageTsConfig)
    {
        $tceForm = &$pageTsConfig['TCEFORM.']['table.'][$table . '.'];
        $tca = &$GLOBALS['TCA'][$table];

        foreach ((array)$element['defaults.'] as $column => $value) {
            if (is_array($tca['columns'][$column])) {
                // get information about if the field value is OK
                $config = &$tca['columns'][$column]['config'];
                $authModeDeny = $config['type'] == 'select' && $config['authMode']
                    && !$this->getBackendUserAuthentication()->checkAuthMode($table, $column, $value, $config['authMode']);
                // explode TsConfig keys only as needed
                if (!isset($removeItems[$column])) {
                    $removeItems[$column] = GeneralUtility::trimExplode(
                        ',',
                        $tceForm[$column]['removeItems'],
                        true
                    );
                }
                if (!isset($keepItems[$column])) {
                    $keepItems[$column] = GeneralUtility::trimExplode(
                        ',',
                        $tceForm[$column]['keepItems'],
                        true
                    );
                }
                $isNotInKeepItems = !empty($keepItems[$column]) && !in_array($value, $keepItems[$column]);

                if ($authModeDeny || $column === $tca['ctrl']['type'] && (in_array($value, $removeItems[$column]) || $isNotInKeepItems)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param array $labels
     * @param array $configuration
     */
    protected function processLabels(array $labels, array &$configuration)
    {
        foreach ($labels as $label) {
            $configuration[$label] = $this->getLanguageService()->sL($configuration[$label]);
        }
    }

    /**
     * @param array $configuration
     * @param string $key
     */
    protected function prepareDependencyOrdering($configuration, $key)
    {
        if (isset($configuration[$key])) {
            $configuration[$key] = GeneralUtility::trimExplode(',', $configuration[$key]);
            $configuration[$key] = array_map(function ($s) {
                return $s . '.';
            }, $configuration[$key]);
        }
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

    /**
     * Returns the current BE user
     *
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected function getBackendUserAuthentication()
    {
        return $GLOBALS['BE_USER'];
    }
}

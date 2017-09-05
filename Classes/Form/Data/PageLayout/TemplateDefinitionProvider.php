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

use TYPO3\CMS\Backend\Configuration\TypoScript\ConditionMatching\ConditionMatcher;
use TYPO3\CMS\Backend\Form\FormDataProviderInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\BackendLayout\DataProviderCollection;
use TYPO3\CMS\Backend\View\BackendLayout\DefaultDataProvider;
use TYPO3\CMS\Backend\View\BackendLayoutView;
use TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;

/**
 * Add the grid template to the grid container using backend layouts
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
        $result['customData']['tx_grid']['template']['areas'] = [];
        $layout = $this->load($result);

        if ($layout !== null) {
            $allowedAreas = trim($result['pageTsConfig']['mod']['SHARED']['properties']['colPos_list'] ?? '');
            $allowedAreas = array_flip(GeneralUtility::intExplode(',', $allowedAreas));

            $parsedPositionItems = GeneralUtility::makeInstance(BackendLayoutView::class)
                ->getColPosListItemsParsed($result['tableName'] === 'pages' ? $result['databaseRow']['uid'] : $result['databaseRow']['pid']);

            self::primeCoordinates($layout['rows'], $layout['rowCount']);

            foreach ($layout['rows'] as $x => &$row) {
                self::primeCoordinates($row['columns'], $layout['colCount']);

                foreach ($row['columns'] as $y => &$column) {
                    if (!isset($column['colPos'])) {
                        $column['colPos'] = StringUtility::getUniqueId();
                    }

                    $column['name'] = GeneralUtility::isFirstPartOfStr($column['name'], 'LLL:') ?
                        $this->getLanguageService()->sL($column['name']) : $column['name'];

                    $column['name'] = BackendUtility::getProcessedValue(
                        $result['customData']['tx_grid']['items']['config']['foreign_table'],
                        $result['customData']['tx_grid']['items']['vanillaTca']['ctrl']['EXT']['tx_grid']['areaField'],
                        $column['colPos']
                    ) ?? $column['name'];

                    $column['name'] = array_reduce($parsedPositionItems, function($label, $item) use ($column) {
                        return $label = ($item[1] == $column['colPos'] ? $this->getLanguageService()->sL($item[0]) : $label);
                    }, '') ?? $column['name'];

                    $previous = end($result['customData']['tx_grid']['template']['areas']);
                    $y = $y > 0 ? $previous['column']['end'] : 0;

                    $result['customData']['tx_grid']['template']['areas'][] = [
                        'uid' => $column['colPos'],
                        'title' => $column['name'],
                        'assigned' => is_numeric($column['colPos']),
                        'row' => [
                            'start' => $x + 1,
                            'end' => $x + ($column['rowspan'] ?? 1)
                        ],
                        'column' => [
                            'start' => $y + 1,
                            'end' => $y + ($column['colspan'] ?? 1)
                        ],
                        'restricted' => $allowedAreas && !isset($allowedAreas[$column['colPos']])
                    ];
                }
            }
        }

        return $result;
    }

    protected function primeCoordinates(array &$coordinates, $maximum)
    {
        $coordinates = array_values(array_filter(
            $coordinates,
            function($coordinate) use ($maximum) {
                return is_numeric($coordinate) && $coordinate <= $maximum;
            },
            ARRAY_FILTER_USE_KEY
        ));
        ksort($coordinates);
    }

    protected function load(array &$result)
    {
        $dataProviderCollection = GeneralUtility::makeInstance(DataProviderCollection::class);

        $dataProviderCollection->add('default', DefaultDataProvider::class);

        foreach ((array)$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['BackendLayoutDataProvider'] as $key => $provider) {
            $dataProviderCollection->add($key, $provider);
        }

        if ($result['tableName'] === 'pages') {
            $selected = $result['databaseRow']['backend_layout'][0];
        } else {
            $selected = $result['parentPageRow']['backend_layout'];
        }

        if ($selected === -1) {
            $selected = false;
        } elseif (empty($selected)) {
            $rootLine = $result['rootline'];
            array_shift($rootLine);
            array_pop($rootLine);
            foreach ($rootLine as $page) {
                $selected = (string)$page['backend_layout_next_level'];
                if ($selected === '-1') {
                    $selected = false;
                    break;
                } elseif ($selected !== '' && $selected !== '0') {
                    break;
                }
            }
        }

        $layout = $dataProviderCollection->getBackendLayout(empty($selected) ? 'default' : $selected, $result[$result['tableName'] === 'pages' ? 'databaseRow' : 'parentPageRow']['uid']);

        if (!empty($selected) && $layout === null) {
            $layout = $dataProviderCollection->getBackendLayout('default', $result[$result['tableName'] === 'pages' ? 'databaseRow' : 'parentPageRow']['uid']);
        }

        if ($layout !== null) {
            $parser = GeneralUtility::makeInstance(TypoScriptParser::class);
            $conditionMatcher = GeneralUtility::makeInstance(ConditionMatcher::class);

            $this->getLanguageService()->includeLLFile('EXT:backend/Resources/Private/Language/locallang_layout.xlf');
            $parser->parse($parser->checkIncludeLines($layout->getConfiguration()), $conditionMatcher);

            $layout = GeneralUtility::removeDotsFromTS((array)$parser->setup['backend_layout.']);
        }

        return $layout;
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

<?php
declare(strict_types=1);
namespace TYPO3\CMS\Grid\Form\Node;

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

use TYPO3Fluid\Fluid\View\ViewInterface;

/**
 * Render the layout of a content container with its overlays
 *
 * This is an entry container called from controllers.
 */
class LayoutOverlayContainer extends LayoutContainer
{
    /**
     * @var string
     */
    protected $templatePathAndFileName = 'EXT:grid/Resources/Private/Templates/Form/Node/LayoutOverlayContainer.html';

    /**
     * @param ViewInterface $view
     */
    protected function prepareView(ViewInterface $view)
    {
        $view->assignMultiple([
            'language' => $this->data['customData']['tx_grid']['language'],
            'areas' => $this->data['customData']['tx_grid']['template']['areas'],
            'uid' => $this->data['vanillaUid'],
            'pid' => $this->data['effectivePid'],
            'actions' => $this->data['customData']['tx_grid']['actions'],
            'overlays' => $this->data['customData']['tx_grid']['overlays'],
            'hidden' => array_filter(iterator_to_array($this->items()), function($item) {
                return !$item['customData']['tx_grid']['visible'];
            }),
            'unused' => array_merge(
                $this->data['customData']['tx_grid']['template']['unused'],
                array_reduce($this->data['customData']['tx_grid']['overlays'], function ($carry, $overlay) {
                    return array_merge($carry, $overlay['customData']['tx_grid']['template']['unused']);
                }, [])
            ),
            'title' => $this->data['recordTitle'],
            'row' => $this->data['databaseRow'],
            'settings' => $this->getUserConfiguration(),
            'tca' => [
                'container' => [
                    'table' => $this->data['tableName'],
                    'field' => $this->data['customData']['tx_grid']['columnToProcess']
                ],
                'element' => [
                    'table' => $this->data['customData']['tx_grid']['items']['config']['foreign_table'],
                    'fields' => [
                        'area' => $this->data['customData']['tx_grid']['items']['config']['grid_area_field'],
                        'language' => $this->data['customData']['tx_grid']['items']['vanillaTca']['ctrl']['languageField'],
                        'foreign' => [
                            'table' => $this->data['customData']['tx_grid']['items']['config']['foreign_table_field'],
                            'field' => $this->data['customData']['tx_grid']['items']['config']['foreign_field']
                        ]
                    ]
                ]
            ]
        ]);
    }

    /**
     * Generate the items to render
     */
    protected function &items()
    {
        foreach ($this->data['customData']['tx_grid']['items']['children'] as &$item) {
            yield $item;
        }

        foreach ($this->data['customData']['tx_grid']['overlays'] as &$overlay) {
            foreach ($overlay['customData']['tx_grid']['items']['children'] as &$item) {
                yield $item;
            }
        }
    }

    /**
     * @return array
     */
    protected function initializeResultArray(): array
    {
        return array_merge_recursive(
            parent::initializeResultArray(),
            [
                'requireJsModules' => ['TYPO3/CMS/Grid/Localization']
            ]
        );
    }
}

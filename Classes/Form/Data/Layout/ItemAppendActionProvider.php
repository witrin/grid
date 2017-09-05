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

use TYPO3\CMS\Backend\Utility\BackendUtility;

/**
 * Add insert after action for grid items of a grid container
 */
class ItemAppendActionProvider extends AreaInsertActionProvider
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
            foreach ($area['items'] as &$item) {
                if ($this->isAvailable($result, ['area' => $area, 'item' => $item])) {
                    $attributes = $this->getAttributes($result, ['area' => $area, 'item' => $item]);
                    $item['customData']['tx_grid']['actions']['append'] = array_merge(
                        $attributes,
                        [
                            'url' => BackendUtility::getModuleUrl($attributes['url']['module'], $attributes['url']['parameters'])
                        ]
                    );
                }
            }
        }

        return $result;
    }

    /**
     * @param array $result
     * @param array $parameters
     * @return array
     */
    protected function getAttributes(array $result, array $parameters) : array
    {
        $attributes = parent::getAttributes($result, $parameters);

        if ($this->useWizard($result)) {
            $attributes['url']['parameters']['ancestorUid'] = $parameters['item']['vanillaUid'];
        } else {
            $attributes['url']['parameters']['edit'][$result['customData']['tx_grid']['items']['config']['foreign_table']] = [
                -(int)$parameters['item']['vanillaUid'] => 'new'
            ];
        }

        return $attributes;
    }
}

<?php
declare(strict_types=1);
namespace TYPO3\CMS\Wireframe\Form\Node;

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
 * Render a content container
 *
 * This is an entry container called from controllers.
 */
class LocalizationContainer extends LayoutContainer
{
    /**
     * @var string
     */
    protected $templatePathAndFileName = 'EXT:wireframe/Resources/Private/Templates/Form/Node/LocalizationContainer.html';

    /**
     * @param ViewInterface $view
     */
    protected function prepareView(ViewInterface $view)
    {
        $languageOverlayUids = $this->data['renderData']['languageOverlayUids'] ?? null;

        if ($languageOverlayUids) {
            uasort($this->data['systemLanguageRows'], function ($a, $b) {
                return $a['sorting'] <=> $b['sorting'];
            });

            $view->assign(
                'languages',
                [$this->data['systemLanguageRows'][0]] + array_filter(
                    $this->data['systemLanguageRows'],
                    function ($languageRow) use ($languageOverlayUids) {
                        return $languageRow['uid'] > 0 && in_array($languageRow['uid'], $languageOverlayUids);
                    }
                )
            );

            foreach ($this->data['customData']['tx_grid']['template']['areas'] as &$area) {
                foreach ($area['overlays'] as &$overlay) {
                    $overlay['items'] = array_filter((array)$area['items'], function(&$item) use ($overlay) {
                        return $item['customData']['tx_grid']['languageUid'] == $overlay['languageUid'];
                    });
                }
                usort($area['overlays'], function($a, $b) {
                    $this->data['systemLanguageRows'][$a['languageUid']]['sorting'] <=>
                        $this->data['systemLanguageRows'][$b['languageUid']]['sorting'];
                });
            }
        }

        parent::prepareView($view);
    }

    /**
     * Filter the item from being rendered
     *
     * @param array $item
     * @return bool
     */
    protected function filterItem(array &$item) {
        return false;
    }

    /**
     * @return array
     */
    protected function initializeResultArray(): array
    {
        return array_merge_recursive(
            parent::initializeResultArray(),
            [
                'requireJsModules' => ['TYPO3/CMS/Wireframe/Localization']
            ]
        );
    }
}

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

/**
 * Add localize action for grid area overlays
 *
 * @deprecated
 * @see https://review.typo3.org/51272
 */
class LocalizeAreaActionProvider extends \TYPO3\CMS\Grid\Form\Data\Layout\LocalizeAreaActionProvider
{
    /**
     * @param array $result
     * @param array $parameters
     * @return array
     */
    protected function getAttributes(array $result, array $parameters) : array
    {
        return [
            'data' => [
                'has-elements' => (int)!empty($parameters['overlay']['items']),
                'allow-copy' => (int)($result['customData']['tx_grid']['localization']['strategy'] === 'unbound' ||
                    $result['customData']['tx_grid']['localization']['strategy'] === null),
                'allow-translate' => (int)($result['customData']['tx_grid']['localization']['strategy'] === 'bound' ||
                    $result['customData']['tx_grid']['localization']['strategy'] === null),
                'container-table' => 'pages',
                'relationship-column' => $result['customData']['tx_grid']['columnToProcess'],
                'area-uid' => $parameters['area']['uid'],
                'language-uid' => $result['customData']['tx_grid']['language']['uid'],
                'language-title' => $result['customData']['tx_grid']['language']['title'],
                'area-title' => $parameters['area']['title'],
                'container-uid' => $result['defaultLanguageRow']['uid']
            ],
            'icon' => 'actions-localize',
            'title' => $this->getLanguageService()->sL('LLL:EXT:lang/Resources/Private/Language/locallang_misc.xlf:localize'),
            'section' => 'header',
            'class' => 't3js-localize'
        ];
    }
}

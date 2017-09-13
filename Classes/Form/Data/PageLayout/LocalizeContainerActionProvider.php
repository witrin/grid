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

use TYPO3\CMS\Backend\Configuration\TranslationConfigurationProvider;
use TYPO3\CMS\Backend\Form\FormDataProviderInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Grid\Utility\TcaUtility;

/**
 * Add localize actions for page
 *
 * @deprecated
 * @see https://review.typo3.org/51272
 */
class LocalizeContainerActionProvider extends \TYPO3\CMS\Grid\Form\Data\Layout\LocalizeContainerActionProvider
{
    /**
     * @param array $result
     * @param array $parameters
     * @return bool
     */
    protected function isAvailable(array $result, array $parameters) : bool
    {
        return true;
    }

    /**
     * @param array $result
     * @param array $parameters
     * @return array
     */
    protected function getAttributes(array $result, array $parameters) : array
    {
        $attributes = parent::getAttributes($result, $parameters);

        $attributes['url']['parameters']['edit']['pages_language_overlay'] = $attributes['url']['parameters']['edit'][$result['tableName']];
        $attributes['url']['parameters']['overrideVals']['pages_language_overlay'] = [
            'doktype' => $result['databaseRow']['doktype'][0],
            'sys_language_uid' => $parameters['language']['uid']
        ];

        unset($attributes['url']['parameters']['edit'][$result['tableName']]);
        unset($attributes['url']['parameters']['defaultVals']);
        unset($attributes['url']['parameters']['overrideVals'][$result['tableName']]);

        return $attributes;
    }
}

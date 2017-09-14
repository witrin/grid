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
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Resolve all unused items.
 */
class UnusedItemsProvider implements FormDataProviderInterface
{
    /**
     * Add data
     *
     * @param array $result
     * @return array
     */
    public function addData(array $result)
    {
        $areaField = $result['customData']['tx_grid']['items']['config']['grid_area_field'];
        $areas = array_flip(array_column((array)$result['customData']['tx_grid']['template']['areas'], 'uid'));
        $result['customData']['tx_grid']['template']['unused'] = [];

        foreach ($result['customData']['tx_grid']['items']['children'] as &$item) {
            $areaUid = is_array($item['databaseRow'][$areaField]) ? $item['databaseRow'][$areaField][0] : $item['databaseRow'][$areaField];

            if (!isset($areas[$areaUid])) {
                $result['customData']['tx_grid']['template']['unused'][] = &$item;
            }
        }

        if (!empty($result['customData']['tx_grid']['template']['unused'])) {
            $this->getFlashMessageQueue($result)->addMessage(
                GeneralUtility::makeInstance(
                    FlashMessage::class,
                    $this->getLanguageService()->sL('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:staleUnusedElementsWarning'),
                    $this->getLanguageService()->sL('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:staleUnusedElementsWarningTitle'),
                    FlashMessage::WARNING
                )
            );

            foreach ($result['customData']['tx_grid']['template']['unused'] as &$item) {
                $item['customData']['tx_grid']['area'] = null;
                unset($item['customData']['tx_grid']['actions']['append']);
            }
        }

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

    /**
     * Returns FlashMessageQueue
     *
     * @param array $data
     * @return \TYPO3\CMS\Core\Messaging\FlashMessageQueue
     */
    protected function getFlashMessageQueue(array $data = null)
    {
        return GeneralUtility::makeInstance(FlashMessageService::class)->getMessageQueueByIdentifier();
    }
}

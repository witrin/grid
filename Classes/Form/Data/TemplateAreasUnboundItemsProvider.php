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
 * Collects all unbound items in a separate template area
 */
class TemplateAreasUnboundItemsProvider implements FormDataProviderInterface
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
        $unboundItems = [];

        foreach ($result['customData']['tx_grid']['items']['children'] as &$item) {
            $areaUid = is_array($item['databaseRow'][$areaField]) ? $item['databaseRow'][$areaField][0] : $item['databaseRow'][$areaField];

            if (!isset($areas[$areaUid]) && isset($result['additionalLanguageRows'][$item['customData']['tx_grid']['languageUid']])) {
                $unboundItems[] = &$item;
            }
        }

        if (!empty($unboundItems)) {
            $this->getFlashMessageService()->getMessageQueueByIdentifier()->addMessage(GeneralUtility::makeInstance(
                FlashMessage::class,
                $this->getLanguageService()->getLL('staleUnusedElementsWarning'),
                $this->getLanguageService()->getLL('staleUnusedElementsWarningTitle'),
                FlashMessage::WARNING
            ));

            $unboundArea = [
                'uid' => 'unused',
                'assigned' => true,
                'virtual' => true,
                'title' => $this->getLanguageService()->sL('LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:colPos.I.unused'),
                'column' => [
                    'start' => 1,
                    'end' => $result['customData']['tx_grid']['template']['columns']
                ],
                'row' => [
                    'start' => ++$result['customData']['tx_grid']['template']['rows'],
                    'end' => $result['customData']['tx_grid']['template']['rows']
                ],
                'items' => $unboundItems,
                'overlays' => []
            ];

            foreach ($unboundItems as &$item) {
                $item['customData']['tx_grid']['area'] = &$unboundArea;
                unset($item['customData']['tx_grid']['actions']['append']);
            }

            $result['customData']['tx_grid']['template']['areas'][] = &$unboundArea;
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
     * Returns FlashMessageService
     *
     * @return \TYPO3\CMS\Core\Messaging\FlashMessageService
     */
    protected function getFlashMessageService()
    {
        return GeneralUtility::makeInstance(FlashMessageService::class);
    }
}

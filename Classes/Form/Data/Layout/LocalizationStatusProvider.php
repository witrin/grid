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
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Grid\Form\Data\Traits\FlashMessageProvider;

/**
 * Determines the localization status for the given grid areas
 */
class LocalizationStatusProvider implements FormDataProviderInterface
{
    use FlashMessageProvider;

    /**
     * Add data
     *
     * @param array $result
     * @return array
     */
    public function addData(array $result)
    {
        if ($result['customData']['tx_grid']['language']['uid'] > 0) {
            $status = [];

            foreach ($result['customData']['tx_grid']['items']['children'] as &$item) {
                $item['customData']['tx_grid']['localization']['status'] = $item['defaultLanguageRow'] === null ? 'unbound' : 'bound';
                $status[$item['customData']['tx_grid']['localization']['status']] = true;
            }

            if (empty($status)) {
                $status['unknown'] = true;
            }

            $result['customData']['tx_grid']['localization']['status'] = count($status) > 1 ? 'mixed' : key($status);

            if (
                $result['customData']['tx_grid']['localization']['status'] !== 'unknown' &&
                $result['customData']['tx_grid']['localization']['status'] !== 'bound' &&
                $result['customData']['tx_grid']['localization']['mode'] === 'strict'
            ) {
                $this->getFlashMessageQueue(
                    $result['tableName'],
                    $result['customData']['tx_grid']['columnToProcess'],
                    $result['customData']['tx_grid']['language']['uid']
                )->addMessage(
                    GeneralUtility::makeInstance(
                        FlashMessage::class,
                        sprintf(
                            $this->getLanguageService()->sL('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:staleTranslationWarning'),
                            $result['systemLanguageRows'][$result['customData']['tx_grid']['language']['uid']]['title']
                        ),
                        sprintf(
                            $this->getLanguageService()->sL('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:staleTranslationWarningTitle'),
                            $result['systemLanguageRows'][$result['customData']['tx_grid']['language']['uid']]['title']
                        ),
                        FlashMessage::WARNING
                    )
                );
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
}

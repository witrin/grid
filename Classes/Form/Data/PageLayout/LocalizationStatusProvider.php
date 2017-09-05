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

use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Grid\Controller\PageLayoutController;

/**
 * Determines the localization status for the given grid areas
 *
 * @deprecated
 * @see https://review.typo3.org/51272
 */
class LocalizationStatusProvider extends \TYPO3\CMS\Grid\Form\Data\Layout\LocalizationStatusProvider
{
    /**
     * Returns FlashMessageQueue
     *
     * @param array $data
     * @return \TYPO3\CMS\Core\Messaging\FlashMessageQueue
     */
    protected function getFlashMessageQueue(array $data = null)
    {
        return GeneralUtility::makeInstance(FlashMessageService::class)->getMessageQueueByIdentifier(
            sprintf(PageLayoutController::OVERLAY_FLASH_MESSAGE_QUEUE, $data['customData']['tx_grid']['language']['uid'])
        );
    }
}

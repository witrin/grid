<?php
declare(strict_types=1);
namespace TYPO3\CMS\Grid\Form\Data\Traits;

use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Grid\Form\Data\Utility\FlashMessageUtility;

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
 * Resolve all unused items.
 */
trait FlashMessageProvider
{
    /**
     * Returns FlashMessageQueue
     *
     * @param array $result
     * @return \TYPO3\CMS\Core\Messaging\FlashMessageQueue
     */
    protected function getFlashMessageQueue($tableName = null, $fieldName = null, $languageUid = -1)
    {
        return GeneralUtility::makeInstance(FlashMessageService::class)->getMessageQueueByIdentifier(
            sprintf(FlashMessageUtility::QUEUE_IDENTIFIER, $tableName, $fieldName, $languageUid)
        );
    }
}

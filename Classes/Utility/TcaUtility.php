<?php
namespace TYPO3\CMS\Grid\Utility;

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
 * Tca Utility
 */
class TcaUtility
{
    /**
     * Filter hidden fields
     *
     * @param array $tca The (partial) TCA for the fields respectively columns
     * @param array $fields The array to filter with keys as the field names
     * @return array All entries from $fields which are not hidden through the TCA
     */
    static public function filterHiddenFields(array $tca, array $fields)
    {
        return array_filter(
            $fields,
            function($field) use ($tca) {
                return isset($tca[$field]) && $tca[$field]['config']['type'] !== 'passthrough';
            },
            ARRAY_FILTER_USE_KEY
        );
    }
}
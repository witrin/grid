<?php
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

/**
 * Resolve and prepare inline data.
 */
class TcaInline extends \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInline
{

    /**
     * Extends the default data provider for inline records by adding children in the requested additional languages.
     *
     * @param array $result Result array
     * @param string $fieldName Current handle field name
     * @return array Modified item array
     */
    protected function resolveRelatedRecords(array $result, $fieldName)
    {
        $result = parent::resolveRelatedRecords($result, $fieldName);

        if ($result['customData']['tx_grid']['columnToProcess'] === $fieldName && $result['command'] === 'edit') {
            $childTableName = $result['processedTca']['columns'][$fieldName]['config']['foreign_table'];
            $connectedUids = [];

            foreach ($result['additionalLanguageRows'] as $additionalLanguageRow) {
                $connectedUids += $this->resolveConnectedRecordUids(
                    $result['processedTca']['columns'][$fieldName]['config'],
                    $result['tableName'],
                    $additionalLanguageRow['uid'],
                    $additionalLanguageRow[$fieldName]
                );
            }

            $connectedUids = $this->getWorkspacedUids($connectedUids, $childTableName);

            if ($result['inlineCompileExistingChildren']) {
                $result['customData']['tx_grid']['items']['children'] = $result['processedTca']['columns'][$fieldName]['children'];

                foreach ($connectedUids as $childUid) {
                    $result['customData']['tx_grid']['items']['children'][] = $this->compileChild($result, $fieldName, $childUid);
                }

                foreach ($result['customData']['tx_grid']['items']['children'] as &$item) {
                    $item['customData']['tx_grid']['containerProviderList'] = $result['customData']['tx_grid']['containerProviderList'];
                }
            }
        }

        return $result;
    }
}
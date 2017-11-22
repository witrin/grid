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

use TYPO3\CMS\Backend\Form\FormDataCompiler;
use TYPO3\CMS\Backend\Form\FormDataGroup\TcaDatabaseRecord;
use TYPO3\CMS\Backend\Form\FormDataProvider\AbstractDatabaseRecordProvider;
use TYPO3\CMS\Backend\Form\FormDataProviderInterface;
use TYPO3\CMS\Backend\Form\InlineStackProcessor;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\RelationHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Creates
 */
class ItemDataProvider extends AbstractDatabaseRecordProvider implements FormDataProviderInterface
{

    /**
     * Add data
     *
     * @param array $result
     * @return array
     */
    public function addData(array $result)
    {
        $fieldName = $result['customData']['tx_grid']['columnToProcess'];
        $fieldConfig = $result['processedTca']['columns'][$fieldName];

        if ($this->isGridField($fieldConfig) && $this->isUserAllowedToModify($fieldConfig)) {
            $result['customData']['tx_grid']['items']['children'] = [];
            $result = $this->resolveRelatedRecords($result, $fieldName);
        }

        return $result;
    }

    /**
     * Is column of type "inline"
     *
     * @param array $fieldConfig
     * @return bool
     */
    protected function isGridField($fieldConfig)
    {
        return !empty($fieldConfig['config']['type']) && $fieldConfig['config']['type'] === 'grid';
    }

    /**
     * Is user allowed to modify child elements
     *
     * @param array $fieldConfig
     * @return bool
     */
    protected function isUserAllowedToModify($fieldConfig)
    {
        return $this->getBackendUser()->check('tables_modify', $fieldConfig['config']['foreign_table']);
    }

    /**
     * Substitute the value in databaseRow of this inline field with an array
     * that contains the databaseRows of currently connected records and some meta information.
     *
     * @param array $result Result array
     * @param string $fieldName Current handle field name
     * @return array Modified item array
     */
    protected function resolveRelatedRecords(array $result, $fieldName)
    {
        $tableName = $result['tableName'];
        $childTableName = $result['processedTca']['columns'][$fieldName]['config']['foreign_table'];
        $connectedUids = [];

        if ($result['command'] === 'edit') {
            $connectedUids = $this->resolveConnectedRecordUids(
                $result['processedTca']['columns'][$fieldName]['config'],
                $tableName,
                $result['processedTca']['columns'][$fieldName]['config']['effectiveParentUid'],
                $result['databaseRow'][$fieldName]
            );
        }

        $result['databaseRow'][$fieldName] = implode(',', $connectedUids);

        foreach ($connectedUids as $connectedUid) {
            $result['customData']['tx_grid']['items']['children'][] = $this->compileChild($result, $fieldName, $connectedUid);
        }

        return $result;
    }

    /**
     * Compile a full child record
     *
     * @param array $result Result array of parent
     * @param string $fieldName Name of parent field
     * @param int $childUid Uid of child to compile
     * @return array Full result array
     */
    protected function compileChild(array $result, $fieldName, $childUid)
    {
        $config = $result['processedTca']['columns'][$fieldName]['config'];
        $childTableName = $config['foreign_table'];

        /** @var InlineStackProcessor $inlineStackProcessor */
        $inlineStackProcessor = GeneralUtility::makeInstance(InlineStackProcessor::class);
        $inlineStackProcessor->initializeByGivenStructure($result['inlineStructure']);
        $inlineTopMostParent = $inlineStackProcessor->getStructureLevel(0);

        /** @var TcaDatabaseRecord $formDataGroup */
        $formDataGroup = GeneralUtility::makeInstance(TcaDatabaseRecord::class);
        /** @var FormDataCompiler $formDataCompiler */
        $formDataCompiler = GeneralUtility::makeInstance(FormDataCompiler::class, $formDataGroup);
        $formDataCompilerInput = [
            'command' => 'edit',
            'tableName' => $childTableName,
            'vanillaUid' => (int)$childUid,
            // Give incoming returnUrl down to children so they generate a returnUrl back to
            // the originally opening record, also see "originalReturnUrl" in inline container
            // and FormInlineAjaxController
            'returnUrl' => $result['returnUrl'],
            'isInlineChild' => true,
            'inlineStructure' => $result['inlineStructure'],
            'inlineExpandCollapseStateArray' => $result['inlineExpandCollapseStateArray'],
            'inlineFirstPid' => $result['inlineFirstPid'],
            'inlineParentConfig' => $config,

            // values of the current parent element
            // it is always a string either an id or new...
            'inlineParentUid' => $config['effectiveParentUid'],
            'inlineParentTableName' => $result['tableName'],
            'inlineParentFieldName' => $fieldName,

            // values of the top most parent element set on first level and not overridden on following levels
            'inlineTopMostParentUid' => $result['inlineTopMostParentUid'] ?: $inlineTopMostParent['uid'],
            'inlineTopMostParentTableName' => $result['inlineTopMostParentTableName'] ?: $inlineTopMostParent['table'],
            'inlineTopMostParentFieldName' => $result['inlineTopMostParentFieldName'] ?: $inlineTopMostParent['field'],
        ];

        return $formDataCompiler->compile($formDataCompilerInput);
    }

    /**
     * Use RelationHandler to resolve connected uids.
     *
     * @param array $parentConfig TCA config section of parent
     * @param string $parentTableName Name of parent table
     * @param string $parentUid Uid of parent record
     * @param string $parentFieldValue Database value of parent record of this inline field
     * @return array Array with connected uids
     */
    protected function resolveConnectedRecordUids(array $parentConfig, $parentTableName, $parentUid, $parentFieldValue)
    {
        $parentUid = $this->getLiveDefaultId($parentTableName, $parentUid);
        /** @var RelationHandler $relationHandler */
        $relationHandler = GeneralUtility::makeInstance(RelationHandler::class);

        $relationHandler->start($parentFieldValue, $parentConfig['foreign_table'], '', $parentUid, $parentTableName, $parentConfig);
        return $relationHandler->getValueArray();
    }

    /**
     * Gets the record uid of the live default record. If already
     * pointing to the live record, the submitted record uid is returned.
     *
     * @param string $tableName
     * @param int $uid
     * @return int
     * @todo: the workspace mess still must be resolved somehow
     */
    protected function getLiveDefaultId($tableName, $uid)
    {
        $liveDefaultId = BackendUtility::getLiveVersionIdOfRecord($tableName, $uid);
        if ($liveDefaultId === null) {
            $liveDefaultId = $uid;
        }
        return $liveDefaultId;
    }

    /**
     * @return BackendUserAuthentication
     */
    protected function getBackendUser()
    {
        return $GLOBALS['BE_USER'];
    }
}

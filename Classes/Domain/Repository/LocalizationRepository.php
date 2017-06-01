<?php
namespace TYPO3\CMS\Wireframe\Domain\Repository;

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

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\BackendWorkspaceRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Repository for record localizations
 */
class LocalizationRepository
{

    /**
     * Fetches the language from which the inline records of a grid area in a certain language were initially localized
     *
     * @param int $containerUid
     * @param int $areaUid
     * @param int $languageUid
     * @param string $containerTable
     * @param string $itemsField
     * @return array|false
     */
    public function fetchOriginLanguage($containerUid, $areaUid, $languageUid, $containerTable = 'pages', $itemsField = 'content')
    {
        $this->validateTableConfiguration($containerTable, $itemsField);

        $itemTable = $GLOBALS['TCA'][$containerTable]['columns'][$itemsField]['config']['foreign_table'];
        $contentTableConfiguration = $GLOBALS['TCA'][$itemTable];
        $queryBuilder = $this->getQueryBuilderWithWorkspaceRestriction($itemTable);
        $constraints = $this->getItemRecordConstraints($containerTable, $itemsField, $queryBuilder, $containerUid, $areaUid, $languageUid);

        $constraints += $this->getAllowedLanguageConstraintsForBackendUser();

        $queryBuilder->select($itemTable . '_orig.' . $contentTableConfiguration['ctrl']['languageField'])
            ->from($itemTable)
            ->join(
                $itemTable,
                $itemTable,
                $itemTable . '_orig',
                $queryBuilder->expr()->eq(
                    $itemTable . '.' . $contentTableConfiguration['ctrl']['translationSource'],
                    $queryBuilder->quoteIdentifier($itemTable . '_orig.uid')
                )
            )
            ->join(
                $itemTable . '_orig',
                'sys_language',
                'sys_language',
                $queryBuilder->expr()->eq(
                    $itemTable . '_orig.' . $contentTableConfiguration['ctrl']['languageField'],
                    $queryBuilder->quoteIdentifier('sys_language.uid')
                )
            )
            ->where(...$constraints)
            ->groupBy($itemTable . '_orig.' . $contentTableConfiguration['ctrl']['languageField']);

        return $queryBuilder->execute()->fetch();
    }

    /**
     * Returns number of localized records in given container, grid area and language
     *
     * Records which were added to the language directly (not through translation) are not counted.
     *
     * @param int $containerUid
     * @param int $areaUid
     * @param int $languageUid
     * @param string $containerTable
     * @param string $itemsField
     * @return int
     */
    public function getLocalizedRecordCount($containerUid, $areaUid, $languageUid, $containerTable = 'pages', $itemsField = 'content')
    {
        $this->validateTableConfiguration($containerTable, $itemsField);

        $itemTable = $GLOBALS['TCA'][$containerTable]['columns'][$itemsField]['config']['foreign_table'];
        $contentTableConfiguration = $GLOBALS['TCA'][$itemTable];
        $queryBuilder = $this->getQueryBuilderWithWorkspaceRestriction($itemTable);
        $constraints = $this->getItemRecordConstraints($containerTable, $itemsField, $queryBuilder, $containerUid, $areaUid, $languageUid);

        $constraints[] = $queryBuilder->expr()->neq(
            $itemTable . '.' . $contentTableConfiguration['ctrl']['translationSource'],
            $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)
        );

        $rowCount = $queryBuilder->count('uid')
            ->from($itemTable)
            ->where(...$constraints)
            ->execute()
            ->fetchColumn(0);

        return (int)$rowCount;
    }

    /**
     * Fetches all available languages
     *
     * @param int $containerUid
     * @param int $areaUid
     * @param int $languageUid
     * @param string $containerTable
     * @param string $itemsField
     * @return array
     */
    public function fetchAvailableLanguages($containerUid, $areaUid, $languageUid, $containerTable = 'pages', $itemsField = 'content')
    {
        $this->validateTableConfiguration($containerTable, $itemsField);

        $itemTable = $GLOBALS['TCA'][$containerTable]['columns'][$itemsField]['config']['foreign_table'];
        $queryBuilder = $this->getQueryBuilderWithWorkspaceRestriction($itemTable);
        $constraints = $this->getItemRecordConstraints($containerTable, $itemsField, $queryBuilder, $containerUid, $areaUid, $languageUid);

        $constraints[] = $queryBuilder->expr()->neq(
            'sys_language.uid',
            $queryBuilder->createNamedParameter($languageUid, \PDO::PARAM_INT)
        );
        $constraints += $this->getAllowedLanguageConstraintsForBackendUser();

        $queryBuilder->select('sys_language.uid')
            ->from($itemTable)
            ->from('sys_language')
            ->where(...$constraints)
            ->groupBy('sys_language.uid')
            ->orderBy('sys_language.sorting');

        $result = $queryBuilder->execute()->fetchAll();

        return $result;
    }

    /**
     * Get records for copy process
     *
     * @param int $containerUid
     * @param int $areaUid
     * @param int $destinationLanguageUid
     * @param int $languageUid
     * @param string $fields
     * @param string $containerTable
     * @param string $itemsField
     * @return \Doctrine\DBAL\Driver\Statement
     */
    public function getRecordsToCopyDatabaseResult($containerUid, $areaUid, $destinationLanguageUid, $languageUid, $fields = '*', $containerTable = 'pages', $itemsField = 'content')
    {
        $this->validateTableConfiguration($containerTable, $itemsField);

        $originalUids = [];
        $itemTable = $GLOBALS['TCA'][$containerTable]['columns'][$itemsField]['config']['foreign_table'];
        $contentTableConfiguration = $GLOBALS['TCA'][$itemTable];
        // Get original uid of existing elements triggered language / grid area
        $queryBuilder = $this->getQueryBuilderWithWorkspaceRestriction($itemTable);
        $constraints = $this->getItemRecordConstraints($containerTable, $itemsField, $queryBuilder, $containerUid, $areaUid, $destinationLanguageUid);

        $originalUidsStatement = $queryBuilder
            ->select($contentTableConfiguration['ctrl']['translationSource'])
            ->from($itemTable)
            ->where(...$constraints)
            ->execute();

        while ($origUid = $originalUidsStatement->fetchColumn(0)) {
            $originalUids[] = (int)$origUid;
        }

        $constraints = $this->getItemRecordConstraints($containerTable, $itemsField, $queryBuilder, $containerUid, $areaUid, $languageUid);

        $queryBuilder->select(...GeneralUtility::trimExplode(',', $fields, true))
            ->from($itemTable)
            ->where(...$constraints)
            ->orderBy($itemTable . '.' . $contentTableConfiguration['ctrl']['sortby']);

        if (!empty($originalUids)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->notIn(
                    $itemTable . '.uid',
                    $queryBuilder->createNamedParameter($originalUids, Connection::PARAM_INT_ARRAY)
                )
            );
        }

        return $queryBuilder->execute();
    }

    /**
     * Builds additional query constraints to exclude hidden languages and limit a backend user to its allowed languages (unless the user is an admin)
     *
     * @return array
     */
    protected function getAllowedLanguageConstraintsForBackendUser(): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_language');
        $constraints = [];

        $backendUser = $this->getBackendUser();
        if (!$backendUser->isAdmin()) {
            if (!empty($GLOBALS['TCA']['sys_language']['ctrl']['enablecolumns']['disabled'])) {
                $constraints[] = $queryBuilder->expr()->eq(
                    'sys_language.' . $GLOBALS['TCA']['sys_language']['ctrl']['enablecolumns']['disabled'],
                    $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)
                );
            }

            if (!empty($backendUser->user['allowed_languages'])) {
                $constraints[] = $queryBuilder->expr()->in(
                    'sys_language.uid',
                    $queryBuilder->createNamedParameter(
                        GeneralUtility::intExplode(',', $backendUser->user['allowed_languages'], true),
                        Connection::PARAM_INT_ARRAY
                    )
                );
            }
        }

        return $constraints;
    }

    /**
     * Returns the current BE user.
     *
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected function getBackendUser()
    {
        return $GLOBALS['BE_USER'];
    }

    /**
     * Gets a QueryBuilder for the given table with preconfigured restrictions to not retrieve workspace placeholders or deleted records
     *
     * @param string $table
     * @throws \TYPO3\CMS\Core\Exception
     * @return \TYPO3\CMS\Core\Database\Query\QueryBuilder
     */
    protected function getQueryBuilderWithWorkspaceRestriction(string $table): QueryBuilder
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);

        $queryBuilder->getRestrictions()
            ->removeAll()
            ->add(GeneralUtility::makeInstance(DeletedRestriction::class))
            ->add(GeneralUtility::makeInstance(BackendWorkspaceRestriction::class));

        return $queryBuilder;
    }

    /**
     * Builds query constraints for the given grid relationship
     *
     * @param string $containerTable
     * @param string $itemsField
     * @param QueryBuilder $queryBuilder
     * @param int $containerUid
     * @param int $areaUid
     * @param int $languageUid
     * @throws \TYPO3\CMS\Core\Exception
     * @return array
     */
    protected function getItemRecordConstraints(string $containerTable, string $itemsField, QueryBuilder $queryBuilder, $containerUid, $areaUid, $languageUid)
    {
        $itemsConfiguration = $GLOBALS['TCA'][$containerTable]['columns'][$itemsField]['config'];
        $itemsTable = $itemsConfiguration['foreign_table'];
        $contentTableConfiguration = $GLOBALS['TCA'][$itemsTable];

        $constraints = [
            $queryBuilder->expr()->eq(
                $itemsTable . '.' . $contentTableConfiguration['ctrl']['languageField'],
                $queryBuilder->createNamedParameter($languageUid, \PDO::PARAM_INT)
            ),
            $queryBuilder->expr()->eq(
                $itemsTable . '.' . $itemsConfiguration['grid_area_field'],
                $queryBuilder->createNamedParameter($areaUid, \PDO::PARAM_INT)
            ),
            $queryBuilder->expr()->eq(
                $itemsTable . '.' . $GLOBALS['TCA'][$containerTable]['columns'][$itemsField]['config']['foreign_field'],
                $queryBuilder->createNamedParameter($containerUid, \PDO::PARAM_INT)
            )
        ];

        if (isset($GLOBALS['TCA'][$containerTable]['columns'][$itemsField]['config']['foreign_table_field'])) {
            $constraints[] = $queryBuilder->expr()->eq(
                $itemsTable . '.' . $GLOBALS['TCA'][$containerTable]['columns'][$itemsField]['config']['foreign_table_field'],
                $queryBuilder->createNamedParameter($containerTable, \PDO::PARAM_INT)
            );
        }

        if (isset($GLOBALS['TCA'][$containerTable]['columns'][$itemsField]['config']['foreign_match_fields'])) {
            foreach ($GLOBALS['TCA'][$containerTable]['columns'][$itemsField]['config']['foreign_match_fields'] as $field => $value) {
                $constraints[] = $queryBuilder->expr()->eq(
                    $itemsTable . '.' . $field,
                    $queryBuilder->createNamedParameter($value)
                );
            }
        }

        return $constraints;
    }

    /**
     * Validates the configuration for the given inline field
     *
     * @param string $containerTable
     * @param string $itemsField
     * @throws \UnexpectedValueException
     */
    protected function validateTableConfiguration(string $containerTable, string $itemsField)
    {
        if (!isset($GLOBALS['TCA'][$containerTable]['columns'][$itemsField]['config']['foreign_table'])) {
            throw new \UnexpectedValueException('Foreign table is not set.', 1492844896);
        }

        $itemTable = $GLOBALS['TCA'][$containerTable]['columns'][$itemsField]['config']['foreign_table'];

        if (!isset($GLOBALS['TCA'][$itemTable])) {
            throw new \UnexpectedValueException('Foreign table ' . $itemTable . ' does not exist.', 1492886421);
        }

        if (!isset($GLOBALS['TCA'][$containerTable]['columns'][$itemsField]['config']['grid_area_field'])) {
            throw new \UnexpectedValueException('Grid area field is not configured for ' . $itemTable . '.', 1492855178);
        }

        if (!isset($GLOBALS['TCA'][$itemTable]['ctrl']['languageField'])) {
            throw new \UnexpectedValueException('Language field is not configured for ' . $itemTable . '.', 1492855014);
        }

        if (!isset($GLOBALS['TCA'][$itemTable]['ctrl']['translationSource'])) {
            throw new \UnexpectedValueException('Translation source is not configured for ' . $itemTable . '.', 1492855014);
        }
    }
}

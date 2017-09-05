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

use TYPO3\CMS\Backend\Form\FormDataProviderInterface;
use TYPO3\CMS\Backend\Module\ModuleLoader;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\ViewHelpers\Be\InfoboxViewHelper;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * Resolve information about the current page for the editor
 *
 * @todo Markup is not separated from view logic
 */
class PageInfoProvider implements FormDataProviderInterface
{

    /**
     * Add form data to result array
     *
     * @param array $result Initialized result array
     * @return array Result filled with more data
     */
    public function addData(array $result)
    {
        switch($result['databaseRow']['doktype'][0]) {
            case PageRepository::DOKTYPE_SYSFOLDER:
                $moduleLoader = GeneralUtility::makeInstance(ModuleLoader::class);
                $moduleLoader->load($GLOBALS['TBE_MODULES']);

                if (is_array($moduleLoader->modules['web']['sub']['list'])) {
                    $result['customData']['tx_grid']['info'] = [
                        'title' => $this->getLanguageService()->sL('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:goToListModule'),
                        'message' => sprintf(
                            '<p>%s</p><a class="btn btn-info" href="javascript:top.goToModule(\'web_list\',1);">%s</a>',
                            $this->getLanguageService()->sL('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:goToListModuleMessage'),
                            $this->getLanguageService()->sL('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:goToListModule')
                        ),
                        'state' => InfoboxViewHelper::STATE_INFO
                    ];
                }
                break;
            case PageRepository::DOKTYPE_SHORTCUT:
                if (!empty($result['databaseRow']['shortcut']) || $result['databaseRow']['shortcut_mode'][0]) {
                    if ($result['databaseRow']['shortcut_mode'][0] === PageRepository::SHORTCUT_MODE_RANDOM_SUBPAGE) {
                        $result['customData']['tx_grid']['info'] = [
                            'title' => $result['recordTitle'],
                            'message' => sprintf(
                                $this->getLanguageService()->sL('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:pageIsRandomInternalLinkMessage'),
                                htmlspecialchars(
                                    $result['processedTca']['columns']['shortcut_mode']['config']['items'][$result['databaseRow']['shortcut_mode'][0]][0]
                                )
                            ),
                            'state' => InfoboxViewHelper::STATE_INFO
                        ];
                    } else {
                        $pageRepository = GeneralUtility::makeInstance(PageRepository::class);
                        $targetPage = [];

                        switch ($result['databaseRow']['shortcut_mode'][0]) {
                            case PageRepository::SHORTCUT_MODE_NONE:
                                $targetPage = $pageRepository->getPage($result['databaseRow']['shortcut'][0]['uid']);
                                break;
                            case PageRepository::SHORTCUT_MODE_FIRST_SUBPAGE:
                                $targetPage = reset($pageRepository->getMenu($result['databaseRow']['shortcut'][0]['uid'] ?: $result['vanillaUid']));
                                break;
                            case PageRepository::SHORTCUT_MODE_PARENT_PAGE:
                                $targetPage = $pageRepository->getPage($result['databaseRow']['pid']);
                                break;
                        }

                        $result['customData']['tx_grid']['info'] = [
                            'title' => $result['recordTitle'],
                            'message' => sprintf(
                                $this->getLanguageService()->sL('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:pageIsInternalLinkMessage'),
                                sprintf(
                                    '<a href="%s">%s</a>',
                                    htmlspecialchars(
                                        GeneralUtility::linkThisScript(['id' => $result['databaseRow']['shortcut'][0]['uid']])
                                    ),
                                    htmlspecialchars(
                                        BackendUtility::getRecordPath(
                                            $targetPage['uid'],
                                            $this->getBackendUserAuthentication()->getPagePermsClause(Permission::PAGE_SHOW),
                                            1000
                                        )
                                    )
                                )
                            ),
                            'state' => InfoboxViewHelper::STATE_INFO
                        ];
                    }

                    $result['customData']['tx_grid']['info']['message'] = sprintf(
                        '%s (%s)',
                        $result['customData']['tx_grid']['info']['message'],
                        htmlspecialchars(
                            $result['processedTca']['columns']['shortcut_mode']['config']['items'][$result['databaseRow']['shortcut_mode'][0]][0]
                        )
                    );
                } else if ($result['databaseRow']['shortcut_mode'][0] !== PageRepository::SHORTCUT_MODE_RANDOM_SUBPAGE) {
                    $result['customData']['tx_grid']['info'] = [
                        'title' => $this->getLanguageService()->sL('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:pageIsMisconfigured'),
                        'message' => $this->getLanguageService()->sL('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:pageIsMisconfiguredInternalLinkMessage'),
                        'state' => InfoboxViewHelper::STATE_ERROR
                    ];
                }
                break;
            case PageRepository::DOKTYPE_LINK:
                if (empty($result['databaseRow']['url'])) {
                    $result['customData']['tx_grid']['info'] = [
                        'title' => $this->getLanguageService()->sL('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:pageIsMisconfigured'),
                        'message' => $this->getLanguageService()->sL('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:pageIsMisconfiguredExternalLinkMessage'),
                        'state' => InfoboxViewHelper::STATE_ERROR
                    ];
                } else {
                    $externalUrl = GeneralUtility::makeInstance(PageRepository::class)->getExtURL($result['databaseRow']);

                    if ($externalUrl !== false) {
                        $result['customData']['tx_grid']['info'] = [
                            'title' => $this->getLanguageService()->sL('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:pageIsLink'),
                            'message' => sprintf(
                                $this->getLanguageService()->sL('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:pageIsExternalLinkMessage'),
                                sprintf(
                                    '<a href="%s" target="_blank" rel="noopener">%s</a>',
                                    htmlspecialchars($externalUrl),
                                    htmlspecialchars($externalUrl)
                                )
                            ),
                            'state' => InfoboxViewHelper::STATE_INFO
                        ];
                    }
                }
                break;
        }

        if ($result['databaseRow']['content_from_pid']) {
            $page = BackendUtility::getRecord('pages', (int)$result['databaseRow']['content_from_pid']);
            $title = BackendUtility::getRecordTitle('pages', $page);
            $result['customData']['tx_grid']['info'] = [
                'title' => $title,
                'message' => sprintf(
                    $this->getLanguageService()->sL('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:content_from_pid_title'),
                    $this->getPageLink($page, $title)
                ),
                'state' => InfoboxViewHelper::STATE_INFO
            ];
        } else {
            $links = $this->getPageLinksWhereContentIsAlsoShownOn($result['vanillaUid']);
            if (!empty($links)) {
                $result['customData']['tx_grid']['info'] = [
                    'title' => '',
                    'message' => sprintf(
                        $this->getLanguageService()->sL('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:content_on_pid_title'),
                        $links
                    ),
                    'state' => InfoboxViewHelper::STATE_INFO
                ];
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
     * @return BackendUserAuthentication
     */
    protected function getBackendUserAuthentication()
    {
        return $GLOBALS['BE_USER'];
    }

    /**
     * Get all pages with links where the content of a page $pageUid is also shown on
     *
     * @param int $pageUid
     * @return string
     */
    protected function getPageLinksWhereContentIsAlsoShownOn($pageUid)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
        $queryBuilder->getRestrictions()->removeAll();
        $queryBuilder->getRestrictions()->add(GeneralUtility::makeInstance(DeletedRestriction::class));
        $queryBuilder
            ->select('*')
            ->from('pages')
            ->where($queryBuilder->expr()->eq('content_from_pid', $queryBuilder->createNamedParameter($pageUid, \PDO::PARAM_INT)));

        $links = [];
        $rows = $queryBuilder->execute()->fetchAll();

        foreach ((array)$rows as $row) {
            $links[] = $this->getPageLink($row);
        }

        return implode(', ', $links);
    }

    /**
     * @param array $page
     * @param string $title
     * @return string
     */
    protected function getPageLink(array $page, $title = null)
    {
        $linkToPid = GeneralUtility::linkThisScript(['id' => $page['uid']]);
        $title = $title ?? BackendUtility::getRecordTitle('pages', $page);
        return sprintf(
            '<a href="%s">%s (PID %s)</a>',
            htmlspecialchars($linkToPid),
            htmlspecialchars($title),
            (int)$page['uid']
        );
    }
}

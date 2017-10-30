<?php
declare(strict_types=1);
namespace TYPO3\CMS\Grid\Form\Node;

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

use TYPO3\CMS\Backend\Form\NodeResolverInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\PageLayoutView;
use TYPO3\CMS\Backend\View\PageLayoutViewDrawItemHookInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\EndTimeRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\StartTimeRestriction;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Lang\LanguageService;

/**
 * Generates the preview of a page content element
 *
 */
class PageContentPreview extends ContentPreview implements NodeResolverInterface
{
    /**
     * @var PageLayoutView
     * @deprecated
     */
    protected static $pageLayoutView = null;

    /**
     * Render the preview
     *
     * @return array As defined in initializeResultArray() of AbstractNode
     */
    public function render()
    {
        $content = null;
        $result = $this->initializeResultArray();
        $view = $this->initializeView();

        $header = $this->renderHeader();
        $content = $this->processHook($header);

        if ($content === null) {
            $content = $this->renderContent();
        }

        if ($content === null) {
            $content = $this->renderDefaultContent();
        }

        $view->assignMultiple([
            'table' => $this->data['tableName'],
            'language' => $this->data['customData']['tx_grid']['language']['uid'],
            'flag' => $this->data['renderData']['showFlag'] ? $this->data['customData']['tx_grid']['language']['flagIconIdentifier'] : '',
            'record' => $this->data['vanillaUid'],
            'actions' => $this->data['customData']['tx_grid']['actions'],
            'wizard' => !$this->data['disableContentElementWizard'],
            'position' => $this->data['customData']['tx_grid']['areaUid'],
            'visible' => $this->data['customData']['tx_grid']['visible'],
            'errors' => $this->data['renderData']['hasErrors'],
            'warnings' => $this->data['renderData']['hasWarnings'],
            'data' => $this->data['databaseRow'],
            'content' => $header . '<span class="exampleContent">' . $content . '</span>'
        ]);

        $result['html'] = $view->render();

        return $result;
    }

    /**
     * Main resolver method
     *
     * @return string|void New class name or void if this resolver does not change current class name.
     */
    public function resolve()
    {
        if ($this->data['tableName'] === 'tt_content' && $this->data['inlineParentTableName'] === 'pages') {
            return self::class;
        }
    }

    /**
     * @return IconFactory
     */
    protected function getIconFactory()
    {
        return GeneralUtility::makeInstance(IconFactory::class);
    }

    /**
     * Returns the language service
     * @return LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }

    /**
     * @return PageLayoutView
     * @deprecated
     */
    protected function getPageLayoutView()
    {
        if (self::$pageLayoutView === null) {
            self::$pageLayoutView = GeneralUtility::makeInstance(PageLayoutView::class);

            // pretty nasty legacy stuff
            self::$pageLayoutView->CType_labels = [];
            foreach ($GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'] as $val) {
                self::$pageLayoutView->CType_labels[$val[1]] = $this->getLanguageService()->sL($val[0]);
            }
            self::$pageLayoutView->itemLabels = [];
            foreach ($GLOBALS['TCA']['tt_content']['columns'] as $name => $val) {
                self::$pageLayoutView->itemLabels[$name] = $this->getLanguageService()->sL($val['label']);
            }
        }

        return self::$pageLayoutView;
    }

    /**
     * @return string
     */
    protected function renderHeader()
    {
        $html = '';

        if ($this->data['databaseRow']['header']) {
            $note = '';
            if ((int)$this->data['databaseRow']['header_layout'] === 100) {
                $note = ' <em>[' . htmlspecialchars($this->getLanguageService()->sL('LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.hidden')) . ']</em>';
            }
            $html = $this->data['databaseRow']['date'] ?
                htmlspecialchars(
                    $this->getLanguageService()->sL($GLOBALS['TCA']['tt_content']['columns']['date']['label']) . ' ' .
                    BackendUtility::date($this->data['databaseRow']['date'])
                ) . '<br />' : '';
            $html .= '<strong>' . $this->linkEditContent($this->truncateText($this->data['databaseRow']['header']), $this->data['databaseRow']) .
                $note . '</strong><br />';
        }

        return $html;
    }

    /**
     * @param string $header
     * @return string
     */
    protected function processHook(&$header)
    {
        $hooks = &$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem'];
        $view = $this->getPageLayoutView();
        $html = null;
        $draw = true;

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tt_content');

        $queryBuilder->getRestrictions()
            ->removeByType(HiddenRestriction::class)
            ->removeByType(StartTimeRestriction::class)
            ->removeByType(EndTimeRestriction::class);

        $row = $queryBuilder->select('*')
            ->from('tt_content')
            ->where($queryBuilder->expr()->eq('uid', $this->data['vanillaUid']))
            ->execute()
            ->fetch();

        if (is_array($hooks)) {
            foreach ($hooks as $hookClass) {
                $hookObject = GeneralUtility::getUserObj($hookClass);
                if (!$hookObject instanceof PageLayoutViewDrawItemHookInterface) {
                    throw new \UnexpectedValueException(
                        $hookClass . ' must implement interface ' . PageLayoutViewDrawItemHookInterface::class,
                        1218547409);
                }
                $hookObject->preProcess($view, $draw, $header, $html, $row);
            }
        }

        return !$draw && $html === null ? '' : $html;
    }

    /**
     * @return string
     */
    protected function renderDefaultContent()
    {
        $renderText = function ($text) {
            $text = strip_tags($text);
            $text = GeneralUtility::fixed_lgd_cs($text, 1500);
            return nl2br(htmlspecialchars(trim($text), ENT_QUOTES, 'UTF-8', false));
        };

        $out = '';
        $lines = [];
        $labels = [];
        $row = $this->data['databaseRow'];

        foreach ($GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'] as $item) {
            $labels[$item[1]] = $this->getLanguageService()->sL($item[0]);
        }

        switch ($this->data['recordTypeValue']) {
            case 'header':
                if ($row['subheader']) {
                    $lines[] = $renderText($row['subheader']);
                }
                break;
            case 'bullets':
            case 'table':
                if ($row['bodytext']) {
                    $lines[] = $renderText($row['bodytext']);
                }
                break;
            case 'uploads':
                if ($row['media']) {
                    $lines[] = BackendUtility::thumbCode($row, 'tt_content', 'media');
                }
                break;
            case 'menu':
                $lines[] = '<strong>' . htmlspecialchars($labels[$this->data['recordTypeValue']]) . '</strong>';
                $menuTypeLabel = $this->getLanguageService()->sL(
                    BackendUtility::getLabelFromItemListMerged($row['pid'], 'tt_content', 'menu_type',
                        $row['menu_type'])
                );
                $lines[] = $menuTypeLabel ?: 'invalid menu type';
                if ($row['menu_type'] !== '2' && ($row['pages'] || $row['selected_categories'])) {
                    //$out[] = ':' . $view->generateListForCTypeMenu($row);
                }
                break;
            case 'shortcut':
                if (!empty($row['records'])) {
                    $iconFactory = GeneralUtility::makeInstance(IconFactory::class);
                    $recordList = explode(',', $row['records']);
                    foreach ($recordList as $recordIdentifier) {
                        $split = BackendUtility::splitTable_Uid($recordIdentifier);
                        $tableName = empty($split[0]) ? 'tt_content' : $split[0];
                        $shortcutRecord = BackendUtility::getRecord($tableName, $split[1]);
                        if (is_array($shortcutRecord)) {
                            $icon = $iconFactory->getIconForRecord($tableName, $shortcutRecord, Icon::SIZE_SMALL)->render();
                            $icon = BackendUtility::wrapClickMenuOnIcon(
                                $icon,
                                $tableName,
                                $shortcutRecord['uid'],
                                1,
                                '',
                                '+copy,info,edit,view'
                            );
                            $lines[] = $icon
                                . htmlspecialchars(BackendUtility::getRecordTitle($tableName, $shortcutRecord));
                        }
                    }
                }
                break;
            case 'list':
                $hookArr = [];
                $hookOut = '';
                if (is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info'][$row['list_type']])) {
                    $hookArr = $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info'][$row['list_type']];
                } elseif (is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info']['_DEFAULT'])) {
                    $hookArr = $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info']['_DEFAULT'];
                }
                if (!empty($hookArr)) {
                    $_params = ['pObj' => &$this, 'row' => $row, 'infoArr' => []];
                    foreach ($hookArr as $_funcRef) {
                        $hookOut .= GeneralUtility::callUserFunction($_funcRef, $_params, $this);
                    }
                }
                if ((string)$hookOut !== '') {
                    $lines[] = $hookOut;
                } elseif (!empty($row['list_type'])) {
                    $label = BackendUtility::getLabelFromItemListMerged($row['pid'], 'tt_content', 'list_type',
                        $row['list_type']);
                    if (!empty($label)) {
                        $lines[] = '<strong>' . htmlspecialchars($this->getLanguageService()->sL($label)) . '</strong>';
                    } else {
                        $message = sprintf($this->getLanguageService()->sL('LLL:EXT:lang/locallang_core.xlf:labels.noMatchingValue'),
                            $row['list_type']);
                        $lines[] = '<span class="label label-warning">' . htmlspecialchars($message) . '</span>';
                    }
                } elseif (!empty($row['select_key'])) {
                    $lines[] = htmlspecialchars($this->getLanguageService()->sL(BackendUtility::getItemLabel('tt_content',
                            'select_key')))
                        . ' ' . $row['select_key'];
                } else {
                    $lines[] = '<strong>' . $this->getLanguageService()->getLL('noPluginSelected') . '</strong>';
                }
                $lines[] = $this->getLanguageService()->sL(
                    BackendUtility::getLabelFromItemlist('tt_content', 'pages', $row['pages']),
                    true
                );
                break;
            default:
                $contentType = $labels[$this->data['recordTypeValue']];

                if (isset($contentType)) {
                    $lines[] = '<strong>' . htmlspecialchars($contentType) . '</strong>';
                    if ($row['bodytext']) {
                        $lines[] = $this->truncateText($row['bodytext']);
                    }
                    if ($row['image']) {
                        $lines[] = BackendUtility::thumbCode($row, 'tt_content', 'image');
                    }
                } else {
                    $message = sprintf(
                        $this->getLanguageService()->sL('LLL:EXT:lang/locallang_core.xlf:labels.noMatchingValue'),
                        $this->data['recordTypeValue']
                    );
                    $lines[] = '<span class="label label-warning">' . htmlspecialchars($message) . '</span>';
                }
        }

        foreach ($lines as $line) {
            $out .= '<a href="' . $this->data['actions']['edit'] . '">' . $line . '</a><br/>';
        }

        return empty($out) ? null : $out;
    }

    /**
     * Truncates larger amounts of text (usually from RTE/bodytext fields) with word wrapping etc.
     *
     * @param string $text
     * @return string
     * @deprecated
     */
    protected function truncateText($text)
    {
        $text = strip_tags($text);
        $text = GeneralUtility::fixed_lgd_cs($text, 1500);
        return nl2br(htmlspecialchars(trim($text), ENT_QUOTES, 'UTF-8', false));
    }

    /**
     * Will create a link on the input string and possibly a big button after the string which links to editing in the RTE
     *
     * Used for content element content displayed so the user can click the content.
     *
     * @param string $label String to link. Must be prepared for HTML output.
     * @param array $row The row.
     * @return string If the whole thing was editable $str is return with link around. Otherwise just $str.
     * @deprecated
     * @todo It does not take access rights into account anymore since this is done by the data providers
     */
    protected function linkEditContent($label, $row)
    {
        $urlParameters = [
            'edit' => [
                'tt_content' => [
                    $row['uid'] => 'edit'
                ]
            ],
            'returnUrl' => GeneralUtility::getIndpEnv('REQUEST_URI') . '#element-tt_content-' . $row['uid']
        ];
        $url = BackendUtility::getModuleUrl('record_edit', $urlParameters);

        return '<a href="' . htmlspecialchars($url) . '" title="' . htmlspecialchars($this->getLanguageService()->getLL('edit')) . '">' . $label . '</a>';
    }
}

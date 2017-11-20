<?php
defined('TYPO3_MODE') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addModule(
    \TYPO3\CMS\Grid\Controller\PageLayoutController::MODULE_NAMESPACE,
    \TYPO3\CMS\Grid\Controller\PageLayoutController::MODULE_NAME,
    'top',
    '',
    [
        'routeTarget' => \TYPO3\CMS\Grid\Controller\PageLayoutController::class . '::processRequest',
        'access' => 'user,group',
        'icon' => 'EXT:backend/Resources/Public/Icons/module-page.svg',
        'name' => \TYPO3\CMS\Grid\Controller\PageLayoutController::MODULE_NAMESPACE . '_' . \TYPO3\CMS\Grid\Controller\PageLayoutController::MODULE_NAME,
        'labels' => 'LLL:EXT:grid/Resources/Private/Language/locallang_mod.xlf'
    ]
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess']['grid']
    = \TYPO3\CMS\Grid\Hook\Core\Page\PageRenderer\RenderPreProcessHook::class . '->process';

// Temporary as long as this is not part of `backend`
$GLOBALS['TBE_STYLES']['skins']['backend']['stylesheetDirectories']['grid'] = 'EXT:grid/Resources/Public/Css/';

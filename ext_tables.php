<?php
defined('TYPO3_MODE') or die();

if (TYPO3_MODE === 'BE') {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addModule(
        'web',
        'pageLayout',
        'top',
        '',
        [
            'routeTarget' => \TYPO3\CMS\Grid\Controller\PageLayoutController::class . '::processRequest',
            'access' => 'user,group',
            'icon' => 'EXT:backend/Resources/Public/Icons/module-page.svg',
            'name' => 'web_pageLayout',
            'labels' => 'LLL:EXT:grid/Resources/Private/Language/ext_tables:module.layout.title'
        ]
    );

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess']['grid']
        = \TYPO3\CMS\Grid\Hook\Core\Page\PageRenderer\RenderPreProcessHook::class . '->process';
}

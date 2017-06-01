<?php
namespace TYPO3\CMS\Wireframe\ViewHelpers\Record;

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

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Get an icon for a record
 */
class IconViewHelper extends AbstractViewHelper
{

    /**
     * As this ViewHelper renders HTML, the output must not be escaped.
     *
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * Generate the markup.
     *
     * @param string $table Name of the record table
     * @param array $row Data of an record to the the icon for
     * @param string $size Size of the icon
     * @param bool $contextMenu Enables the context menu if allowed
     * @param bool $toolTip Add a tool tip to the icon
     * @return string
     */
    public function render($table, array $row, $size = Icon::SIZE_DEFAULT, $contextMenu = false, $toolTip = false)
    {
        return static::renderStatic(
            [
                'table' => $table,
                'row' => $row,
                'size' => $size,
                'contextMenu' => $contextMenu,
                'toolTip' => $toolTip
            ],
            $this->buildRenderChildrenClosure(),
            $this->renderingContext
        );
    }

    /**
     * Generate the markup.
     *
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $html = GeneralUtility::makeInstance(IconFactory::class)
            ->getIconForRecord($arguments['table'], $arguments['row'], $arguments['size'])
            ->render();

        if ($arguments['toolTip']) {
            $html = '<span ' . BackendUtility::getRecordToolTip($arguments['row'], $arguments['table']) . '>' . $html . '</span>';
        }

        if (
            $arguments['contextMenu'] &&
            self::getBackendUserAuthentication()->recordEditAccessInternals($arguments['table'], $arguments['row'])
        ) {
            $html = BackendUtility::wrapClickMenuOnIcon($html, $arguments['table'], $arguments['row']['uid'], true, '', true);
        }

        return $html;
    }

    /**
     * @return BackendUserAuthentication
     */
    protected static function getBackendUserAuthentication()
    {
        return $GLOBALS['BE_USER'];
    }
}

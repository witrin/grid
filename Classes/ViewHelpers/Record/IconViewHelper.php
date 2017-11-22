<?php
declare(strict_types=1);
namespace TYPO3\CMS\Grid\ViewHelpers\Record;

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
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * Get an icon for a record
 */
class IconViewHelper extends AbstractViewHelper implements CompilableInterface
{
    use CompileWithRenderStatic;

    /**
     * As this ViewHelper renders HTML, the output must not be escaped.
     *
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * Initializes the arguments
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('table', 'string', 'Name of the record table');
        $this->registerArgument('data', 'array', 'Record data');
        $this->registerArgument('size', 'string', 'Size of the icon', false, Icon::SIZE_DEFAULT);
        $this->registerArgument('contextMenu', 'bool', 'Enables the context menu if allowed', false, false);
        $this->registerArgument('toolTip', 'bool', 'Add a tool tip to the icon', false, false);
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
            ->getIconForRecord($arguments['table'], $arguments['data'], $arguments['size'])
            ->render();

        if ($arguments['toolTip']) {
            $html = '<span ' . BackendUtility::getRecordToolTip($arguments['data'], $arguments['table']) . '>' . $html . '</span>';
        }

        if ($arguments['contextMenu']) {
            $html = BackendUtility::wrapClickMenuOnIcon($html, $arguments['table'], $arguments['data']['uid'], true, '', true);
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

<?php
declare(strict_types=1);
namespace TYPO3\CMS\Grid\ViewHelpers\Iterator;

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

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

/**
 * Returns the first element of $haystack.
 */
class FirstViewHelper extends AbstractViewHelper implements CompilableInterface
{
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * @var bool
     */
    protected $escapeChildren = false;

    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('haystack', 'mixed', 'Haystack in which to look for needle');
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @throws Exception
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $haystack = $renderChildrenClosure();
        if (is_array($haystack) === false && $haystack instanceof \Iterator === false && $haystack !== null) {
            throw new Exception(
                'Invalid argument supplied to Iterator/FirstViewHelper - expected array, Iterator or NULL but got ' .
                gettype($haystack),
                1351958398
            );
        }
        if (null === $haystack) {
            return null;
        }
        foreach ($haystack as $needle) {
            return $needle;
        }
        return null;
    }
}

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

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\TagBuilder;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * Renders an action using a link element
 */
class ActionViewHelper extends AbstractViewHelper implements CompilableInterface
{
    use CompileWithRenderStatic;

    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * Initializes the arguments
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('action', 'array', 'The action to be rendered.');
        $this->registerArgument('class', 'string', 'CSS class(es) for this element', false, null);
        $this->registerArgument('title', 'string', 'Tooltip text of element', false, null);
    }

    /**
     * Renders the element
     *
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return mixed
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $action = $arguments['action'];
        $tag = new TagBuilder();

        $tag->setTagName('a');

        $tag->addAttribute('href', $action['url'] ?? '#');
        $tag->addAttribute('class', trim(implode(' ', [(string)$arguments['class'], (string)$action['class']])));

        foreach ((array)$action['data'] as $key => $value) {
            $tag->addAttribute('data-' . $key, is_array($value) || is_object($value) ? json_encode($value) : $value);
        }

        foreach ((array)$action['handler'] as $key => $value) {
            $tag->addAttribute('on' . $key, $value);
        }

        if ($arguments['title']) {
            $tag->addAttribute('title', $action['title']);
        }

        $tag->setContent($renderChildrenClosure());

        return $tag->render();
    }
}

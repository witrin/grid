<?php
declare(strict_types=1);
namespace TYPO3\CMS\Grid\ViewHelpers;

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

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Renders an action using a link element
 */
class TagViewHelper extends AbstractTagBasedViewHelper
{

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

        $this->registerArgument('name', 'string', 'Tag name', false);
    }

    /**
     * Sets the tag name to $this->tagName.
     * Additionally, sets all tag attributes which were registered in
     * $this->tagAttributes and additionalArguments.
     *
     * Will be invoked just before the render method.
     *
     * @api
     */
    public function initialize()
    {
        parent::initialize();

        $this->tag->reset();
        $this->tag->setTagName($this->tagName);

        if ($this->hasArgument('additionalAttributes') && is_array($this->arguments['additionalAttributes'])) {
            $this->tag->addAttributes($this->arguments['additionalAttributes']);
        }

        if ($this->hasArgument('data') && is_array($this->arguments['data'])) {
            foreach ($this->arguments['data'] as $dataAttributeKey => $dataAttributeValue) {
                $this->tag->addAttribute(
                    'data-' . $dataAttributeKey,
                    is_array($dataAttributeValue) || is_object($dataAttributeValue) ? json_encode($dataAttributeValue) : $dataAttributeValue
                );
            }
        }

        if (isset(self::$tagAttributes[static::class])) {
            foreach (self::$tagAttributes[static::class] as $attributeName) {
                if ($this->hasArgument($attributeName) && $this->arguments[$attributeName] !== '') {
                    $this->tag->addAttribute($attributeName, $this->arguments[$attributeName]);
                }
            }
        }
    }

    /**
     * Renders the element
     *
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return mixed
     */
    public function render()
    {
        $name = $this->arguments['name'];
        $content = $this->renderChildren();

        if ($name) {
            $this->tag->setTagName($name);
        }

        if ($content) {
            $this->tag->setContent($content);
        }

        return $this->tag->render();
    }
}

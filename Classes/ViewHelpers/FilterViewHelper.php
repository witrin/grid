<?php
namespace TYPO3\CMS\Wireframe\ViewHelpers;

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
use TYPO3\CMS\Wireframe\Collection\FilterableCollection;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * Get grid items by row or column
 */
class FilterViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * Initializes the arguments
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('data', FilterableCollection::class, 'The data to be filtered by the criteria.');
        $this->registerArgument('by', 'array', 'The criteria to filter the data with.', true);
        $this->registerArgument('as', 'string', 'variable name for the filter result', false, 'result');
    }

    /**
     * Filter grid items by row or column
     *
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return mixed
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $data = $arguments['data'];
        $criteria = $arguments['by'];

        if ($data === null) {
            $data = $renderChildrenClosure();

            if ($data instanceof FilterableCollection) {
                return $data->filterBy($criteria);
            } else if ($data === null) {
                return [];
            } else {
                throw new \InvalidArgumentException('Data must be an instance of ' . FilterableCollection::class, 1493763084);
            }
        } else {
            $renderingContext->getVariableProvider()->add($arguments['as'], $data->filterBy($criteria));
            $result = $renderChildrenClosure();
            $renderingContext->getVariableProvider()->remove($arguments['as']);

            return $result;
        }
    }
}
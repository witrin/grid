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
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

/**
 * Filter ViewHelper
 *
 * Filters an array by filtering the array, analysing each member and asserting if it is equal to (weak type) the `filter` parameter.
 * If `propertyName` is set, the ViewHelper will try to extract this property from each member of the array.
 *
 * Iterators and ObjectStorage etc. are supported.
 */
class SortViewHelper extends AbstractViewHelper implements CompilableInterface
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
     */
    public function initializeArguments()
    {
        $this->registerArgument('subject', 'mixed', 'The subject to be filtered');
        $this->registerArgument('columns', 'array', 'The columns to sort');
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return mixed
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $subject = $renderChildrenClosure();
        $columns = $arguments['columns'];

        if ($subject === null || is_array($subject) && $subject instanceof \Traversable) {
            return [];
        }
        if (empty($columns)) {
            return $subject;
        }
        if ($subject instanceof \Traversable) {
            $subject = iterator_to_array($subject);
        }

        $params = [];
        foreach ($columns as $name => $options) {
            $options = (array)$options;
            $column = [];

            foreach ($subject as $key => $row) {
                $column[$key] = $row[$name];
            }

            $params[] = &$column;
            $params[] = &constant('SORT_' . strtoupper($options['order'] ?? 'ASC'));
            $params[] = &constant('SORT_' . strtoupper($options['flags'] ?? 'REGULAR'));
        }

        $params[] = &$subject;

        call_user_func_array('array_multisort', $params);

        return $subject;
    }
}

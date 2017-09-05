<?php
declare(strict_types=1);
namespace TYPO3\CMS\Grid\Form\Data\PageLayout;

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

/**
 * Add insert action for backend layout column of a page
 */
class AreaInsertActionProvider extends \TYPO3\CMS\Grid\Form\Data\Layout\AreaInsertActionProvider
{
    /**
     * @param array $result
     * @return bool
     */
    protected function useWizard(array $result)
    {
        return (bool)$result['pageTsConfig']['mod.']['web_layout.']['disableNewContentElementWizard'];
    }

    /**
     * @param array $result
     * @param array $parameters
     * @return array
     * @deprecated
     * @see https://review.typo3.org/51272
     */
    protected function getAttributes(array $result, array $parameters) : array
    {
        $attributes = parent::getAttributes($result, $parameters);

        if ($this->useWizard($result)) {
            $attributes['url']['parameters']['containerTable'] = 'pages';
            $attributes['url']['parameters']['containerUid'] = $result['effectivePid'];
        }

        return $attributes;
    }
}

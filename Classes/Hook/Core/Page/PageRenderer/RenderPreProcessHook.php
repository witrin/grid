<?php
namespace TYPO3\CMS\Grid\Hook\Core\Page\PageRenderer;

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

use TYPO3\CMS\Core\Page\PageRenderer;

/**
 * Page renderer hook
 */
class RenderPreProcessHook
{
    /**
     * Process before rendering
     *
     * @param array $parameters
     * @param PageRenderer $pageRenderer
     */
    public function process(&$parameters, PageRenderer $pageRenderer)
    {
        $this->removeJavaScriptModuleLocalization($parameters, $pageRenderer);
    }

    /**
     * Remove the JavaScript module `TYPO3/CMS/Backend/Localization` if its generic replacement is also set
     *
     * @param array $parameters
     * @param PageRenderer $pageRenderer
     * @deprecated
     */
    protected function removeJavaScriptModuleLocalization(&$parameters, PageRenderer $pageRenderer)
    {
        if (
            isset($parameters['jsInline']['RequireJS-Module-TYPO3/CMS/Grid/Localization'])
            && isset($parameters['jsInline']['RequireJS-Module-TYPO3/CMS/Backend/Localization'])
        ) {
            unset($parameters['jsInline']['RequireJS-Module-TYPO3/CMS/Backend/Localization']);
        }
    }
}
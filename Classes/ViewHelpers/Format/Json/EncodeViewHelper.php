<?php
namespace TYPO3\CMS\Wireframe\ViewHelpers\Format\Json;

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

/**
 * Encode an array to JSON
 */
class EncodeViewHelper extends AbstractViewHelper
{

    /**
     * Generate JSON
     *
     * @param array $value
     * @return string
     * @throws \Exception
     */
    public function render($value = null)
    {
        if ($value === null) {
            $value = $this->renderChildren();
        }
        $json = json_encode($value, JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_TAG);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \Exception('The provided argument cannot be converted into JSON.', 1358440181);
        }
        return $json;
    }
}

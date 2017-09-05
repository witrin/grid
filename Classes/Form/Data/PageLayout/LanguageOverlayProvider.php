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
 * Overwrites language overlays for pages and is just a temporary solution
 *
 * @deprecated
 * @see https://review.typo3.org/51272
 */
class LanguageOverlayProvider extends \TYPO3\CMS\Grid\Form\Data\LanguageOverlayProvider
{

    /**
     * Add form data to result array
     *
     * @param array $result Initialized result array
     * @return array Result filled with more data
     */
    public function addData(array $result)
    {
         $result['customData']['tx_grid']['overlays'] = [];

        if ($result['tableName'] === 'pages') {
            $languages = $this->getAdditionalLanguages($result);

            foreach ($languages as $language) {
                $translationInfo = $this->getTranslationProvider()->translationInfo(
                    $result['tableName'],
                    $result['vanillaUid'],
                    $language['uid']
                );

                if (isset($translationInfo['translations'][$language['uid']])) {
                    $formDataCompilerInput = $this->getFormDataCompilerInput(
                        'pages_language_overlay',
                        (int)$translationInfo['translations'][$language['uid']]['uid'],
                        $result
                    );

                    $formDataCompilerInput['inlineResolveExistingChildren'] = false;

                    foreach ($result['customData']['tx_grid']['items']['children'] as $key => $item) {
                        if ($item['customData']['tx_grid']['language']['uid'] === $language['uid']) {
                            $formDataCompilerInput['customData']['tx_grid']['items']['children'][] = $item;
                            unset($result['customData']['tx_grid']['items']['children'][$key]);
                        }
                    }

                    $result['customData']['tx_grid']['overlays'][] = $this->getFormDataCompiler()->compile($formDataCompilerInput);
                }
            }

            foreach ($result['customData']['tx_grid']['items']['children'] as $key => $item) {
                if ($item['customData']['tx_grid']['language']['uid'] !== $result['customData']['tx_grid']['language']['uid']) {
                    unset($result['customData']['tx_grid']['items']['children'][$key]);
                }
            }
        }

        return $result;
    }
}
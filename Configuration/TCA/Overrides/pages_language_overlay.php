<?php

defined('TYPO3_MODE') or die();

$GLOBALS['TCA']['pages_language_overlay']['columns']['content'] = $GLOBALS['TCA']['pages']['columns']['content'];

$GLOBALS['TCA']['pages_language_overlay']['types'][(string)\TYPO3\CMS\Frontend\Page\PageRepository::DOKTYPE_DEFAULT]['showitem'] .= ',content';

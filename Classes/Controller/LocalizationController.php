<?php
namespace TYPO3\CMS\Wireframe\Controller;

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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Configuration\TranslationConfigurationProvider;
use TYPO3\CMS\Wireframe\Domain\Repository\LocalizationRepository;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * LocalizationController handles the AJAX requests for record localization
 */
class LocalizationController
{
    /**
     * @const string
     */
    const ACTION_COPY = 'copyFromLanguage';

    /**
     * @const string
     */
    const ACTION_LOCALIZE = 'localize';

    /**
     * @var IconFactory
     */
    protected $iconFactory;

    /**
     * @var PageRepository
     */
    protected $pageRepository;

    /**
     * @var LocalizationRepository
     */
    protected $localizationRepository;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->iconFactory = GeneralUtility::makeInstance(IconFactory::class);
        $this->localizationRepository = GeneralUtility::makeInstance(LocalizationRepository::class);
        $this->pageRepository = GeneralUtility::makeInstance(PageRepository::class);
    }

    /**
     * Get used languages in a grid area
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function getLanguages(ServerRequestInterface $request, ResponseInterface $response)
    {
        $params = $request->getQueryParams();
        if (!isset($params['containerUid'], $params['areaUid'], $params['languageUid'], $params['containerTable'], $params['relationshipColumn'])) {
            $response = $response->withStatus(500);
            return $response;
        }

        $containerUid = (int)$params['containerUid'];
        $areaUid = (int)$params['areaUid'];
        $language = (int)$params['languageUid'];
        $containerTable = (string)$params['containerTable'];
        $relationshipColumn = (string)$params['relationshipColumn'];
        $containerRecord = $this->pageRepository->getRawRecord($containerTable, $containerUid, 'uid');

        /** @var TranslationConfigurationProvider $translationProvider */
        $translationProvider = GeneralUtility::makeInstance(TranslationConfigurationProvider::class);
        $systemLanguages = $translationProvider->getSystemLanguages($containerRecord['pid']);

        $availableLanguages = [];

        // First check whether column has localized records
        $elementsInColumnCount = $this->localizationRepository->getLocalizedRecordCount($containerUid, $areaUid, $language, $containerTable, $relationshipColumn);

        if ($elementsInColumnCount === 0) {
            $fetchedAvailableLanguages = $this->localizationRepository->fetchAvailableLanguages($containerUid, $areaUid, $language, $containerTable, $relationshipColumn);
            $availableLanguages[] = $systemLanguages[0];

            foreach ($fetchedAvailableLanguages as $fetchedAvailableLanguage) {
                if (isset($systemLanguages[$fetchedAvailableLanguage['uid']])) {
                    $availableLanguages[] = $systemLanguages[$fetchedAvailableLanguage['uid']];
                }
            }
        } else {
            $result = $this->localizationRepository->fetchOriginLanguage($containerUid, $areaUid, $language, $containerTable, $relationshipColumn);
            $availableLanguages[] = $systemLanguages[$result['sys_language_uid']];
        }

        // Pre-render all flag icons
        foreach ($availableLanguages as &$availableLanguage) {
            if ($availableLanguage['flagIcon'] === 'empty-empty') {
                $availableLanguage['flagIcon'] = '';
            } else {
                $availableLanguage['flagIcon'] = $this->iconFactory->getIcon($availableLanguage['flagIcon'], Icon::SIZE_SMALL)->render();
            }
        }

        $response->getBody()->write(json_encode($availableLanguages));
        return $response;
    }

    /**
     * Get a prepared summary of records being translated
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function getSummary(ServerRequestInterface $request, ResponseInterface $response)
    {
        $params = $request->getQueryParams();
        if (!isset($params['containerUid'], $params['areaUid'], $params['destinationLanguageUid'], $params['languageUid'], $params['containerTable'], $params['relationshipColumn'])) {
            $response = $response->withStatus(500);
            return $response;
        }
        
        $containerTable = $params['containerTable'];

        $records = [];
        $result = $this->localizationRepository->getRecordsToCopyDatabaseResult(
            $params['containerUid'],
            $params['areaUid'],
            $params['destinationLanguageUid'],
            $params['languageUid'],
            '*',
            $params['containerTable'],
            $params['relationshipColumn']
        );

        while ($row = $result->fetch()) {
            $records[] = [
                'icon' => $this->iconFactory->getIconForRecord($containerTable, $row, Icon::SIZE_SMALL)->render(),
                'title' => $row[$GLOBALS['TCA'][$containerTable]['ctrl']['label']],
                'uid' => $row['uid']
            ];
        }

        $response->getBody()->write(json_encode($records));
        return $response;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function localizeRecords(ServerRequestInterface $request, ResponseInterface $response)
    {
        $params = $request->getQueryParams();
        if (!isset($params['containerUid'], $params['sourceLanguageUid'], $params['destinationLanguageUid'], $params['action'], $params['contentUids'], $params['containerTable'], $params['relationshipColumn'])) {
            $response = $response->withStatus(500);
            return $response;
        }

        if ($params['action'] !== static::ACTION_COPY && $params['action'] !== static::ACTION_LOCALIZE) {
            $response->getBody()->write('Invalid action "' . $params['action'] . '" called.');
            $response = $response->withStatus(500);
            return $response;
        }

        $this->process($params);

        $response->getBody()->write(json_encode([]));
        return $response;
    }

    /**
     * Processes the localization actions
     *
     * @param array $params
     * @todo Process the items column correctly
     */
    protected function process($params)
    {
        $destinationLanguageUid = (int)$params['destinationLanguageUid'];
        $containerTable = $params['containerTable'];
        $relationshipColumn = $params['relationshipColumn'];
        $contentTable = $GLOBALS['TCA'][$containerTable]['columns'][$relationshipColumn]['config']['foreign_table'];

        // Build command map
        $cmd = [
            $contentTable => []
        ];

        if (is_array($params['contentUids'])) {
            foreach ($params['contentUids'] as $contentUid) {
                if ($params['action'] === static::ACTION_LOCALIZE) {
                    $cmd[$contentTable][$contentUid] = [
                        'localize' => $destinationLanguageUid
                    ];
                } else {
                    $cmd[$contentTable][$contentUid] = [
                        'copyToLanguage' => $destinationLanguageUid,
                    ];
                }
            }
        }

        $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $dataHandler->start([], $cmd);
        $dataHandler->process_cmdmap();
    }
}

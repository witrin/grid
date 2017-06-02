<?php
namespace TYPO3\CMS\Grid\Tests\Functional\Domain\Repository;

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

use TYPO3\CMS\Grid\Domain\Repository\LocalizationRepository;
use TYPO3\CMS\Core\Core\Bootstrap;

/**
 * Test case for TYPO3\CMS\Grid\Domain\Repository\LocalizationRepository
 */
class LocalizationRepositoryTest extends \TYPO3\TestingFramework\Core\Functional\FunctionalTestCase
{
    /**
     * @var LocalizationRepository
     */
    protected $subject;

    /**
     * Sets up this test case.
     */
    protected function setUp()
    {
        $this->testExtensionsToLoad = [
            'typo3conf/ext/grid',
            'typo3conf/ext/grid/Tests/Functional/Fixtures/Extensions/grid_example'
        ];

        parent::setUp();

        $this->setUpBackendUserFromFixture(1);
        Bootstrap::getInstance()->initializeLanguageObject();

        $this->importCSVDataSet(ORIGINAL_ROOT . 'typo3conf/ext/grid/Tests/Functional/Domain/Repository/Fixtures/GridContainerAndItems.csv');

        $this->subject = new LocalizationRepository();
    }

    public function fetchOriginLanguageDefaultDataProvider()
    {
        return [
            'default language returns false' => [
                1,
                0,
                0,
                false
            ],
            'connected mode translated from default language' => [
                1,
                0,
                1,
                false
            ],
            'connected mode translated from non default language' => [
                1,
                0,
                2,
                [
                    'sys_language_uid' => 1
                ]
            ],
            'free mode translated from default language' => [
                2,
                0,
                1,
                false
            ],
            'free mode translated from non default language' => [
                2,
                0,
                2,
                [
                    'sys_language_uid' => 1
                ]
            ],
            'free mode copied from another container translated from default language' => [
                3,
                0,
                1,
                false
            ],
            'free mode copied from another container translated from non default language' => [
                3,
                0,
                2,
                [
                    'sys_language_uid' => 1
                ]
            ]
        ];
    }

    /**
     * @dataProvider fetchOriginLanguageDefaultDataProvider
     * @test
     */
    public function fetchOriginLanguageDefault($containerUid, $areaUid, $localizedLanguage, $expectedResult)
    {
        $result = $this->subject->fetchOriginLanguage($containerUid, $areaUid, $localizedLanguage);
        $this->assertEquals($expectedResult, $result);
    }

    public function fetchOriginLanguageDataProvider()
    {
        return [
            'default language returns false' => [
                1,
                0,
                0,
                'tx_grid_example_domain_model_container',
                'content',
                false
            ],
            'connected mode translated from default language' => [
                1,
                0,
                1,
                'tx_grid_example_domain_model_container',
                'content',
                false
            ],
            'connected mode translated from non default language' => [
                1,
                0,
                2,
                'tx_grid_example_domain_model_container',
                'content',
                [
                    'sys_language_uid' => 1
                ]
            ],
            'free mode translated from default language' => [
                2,
                0,
                1,
                'tx_grid_example_domain_model_container',
                'content',
                false
            ],
            'free mode translated from non default language' => [
                2,
                0,
                2,
                'tx_grid_example_domain_model_container',
                'content',
                [
                    'sys_language_uid' => 1
                ]
            ],
            'free mode copied from another container translated from default language' => [
                3,
                0,
                1,
                'tx_grid_example_domain_model_container',
                'content',
                false
            ],
            'free mode copied from another container translated from non default language' => [
                3,
                0,
                2,
                'tx_grid_example_domain_model_container',
                'content',
                [
                    'sys_language_uid' => 1
                ]
            ]
        ];
    }

    /**
     * @dataProvider fetchOriginLanguageDataProvider
     * @test
     */
    public function fetchOriginLanguage($containerUid, $areaUid, $localizedLanguage, $containerTable, $relationshipColumn, $expectedResult)
    {
        $result = $this->subject->fetchOriginLanguage($containerUid, $areaUid, $localizedLanguage, $containerTable, $relationshipColumn);
        $this->assertEquals($expectedResult, $result);
    }

    public function getLocalizedRecordCountDefaultDataProvider()
    {
        return [
            'default language returns 0 always' => [
                1,
                0,
                0,
                0
            ],
            'connected mode translated from default language' => [
                1,
                0,
                1,
                2
            ],
            'connected mode translated from non default language' => [
                1,
                0,
                2,
                1
            ],
            'free mode translated from default language' => [
                2,
                0,
                1,
                1
            ],
            'free mode translated from non default language' => [
                2,
                0,
                2,
                1
            ],
            'free mode copied from another page translated from default language' => [
                3,
                0,
                1,
                1
            ],
            'free mode copied from another page translated from non default language' => [
                3,
                0,
                2,
                1
            ]
        ];
    }

    /**
     * @dataProvider getLocalizedRecordCountDefaultDataProvider
     * @test
     */
    public function getLocalizedRecordCountDefault($containerUid, $areaUid, $languageUid, $expectedResult)
    {
        $result = $this->subject->getLocalizedRecordCount($containerUid, $areaUid, $languageUid);
        $this->assertEquals($expectedResult, $result);
    }

    public function getLocalizedRecordCountDataProvider()
    {
        return [
            'default language returns 0 always' => [
                1,
                0,
                0,
                'tx_grid_example_domain_model_container',
                'content',
                0
            ],
            'connected mode translated from default language' => [
                1,
                0,
                1,
                'tx_grid_example_domain_model_container',
                'content',
                2
            ],
            'connected mode translated from non default language' => [
                1,
                0,
                2,
                'tx_grid_example_domain_model_container',
                'content',
                1
            ],
            'free mode translated from default language' => [
                2,
                0,
                1,
                'tx_grid_example_domain_model_container',
                'content',
                1
            ],
            'free mode translated from non default language' => [
                2,
                0,
                2,
                'tx_grid_example_domain_model_container',
                'content',
                1
            ],
            'free mode copied from another page translated from default language' => [
                3,
                0,
                1,
                'tx_grid_example_domain_model_container',
                'content',
                1
            ],
            'free mode copied from another page translated from non default language' => [
                3,
                0,
                2,
                'tx_grid_example_domain_model_container',
                'content',
                1
            ]
        ];
    }

    /**
     * @dataProvider getLocalizedRecordCountDataProvider
     * @test
     */
    public function getLocalizedRecordCount($containerUid, $areaUid, $languageUid, $containerTable, $relationshipColumn, $expectedResult)
    {
        $result = $this->subject->getLocalizedRecordCount($containerUid, $areaUid, $languageUid, $containerTable, $relationshipColumn);
        $this->assertEquals($expectedResult, $result);
    }

    public function getRecordsToCopyDatabaseResultDefaultDataProvider()
    {
        return [
            'from language 0 to 1 connected mode' => [
                1,
                0,
                1,
                0,
                [
                    ['uid' => 298]
                ]
            ],
            'from language 1 to 2 connected mode' => [
                1,
                0,
                2,
                1,
                [
                    ['uid' => 300]
                ]
            ],
            'from language 0 to 1 free mode' => [
                2,
                0,
                1,
                0,
                []
            ],
            'from language 1 to 2 free mode' => [
                2,
                0,
                2,
                1,
                []
            ],
            'from language 0 to 1 free mode copied' => [
                3,
                0,
                1,
                0,
                []
            ],
            'from language 1 to 2 free mode  mode copied' => [
                3,
                0,
                2,
                1,
                []
            ],
        ];
    }

    /**
     * @dataProvider getRecordsToCopyDatabaseResultDefaultDataProvider
     * @test
     */
    public function getRecordsToCopyDatabaseResultDefault($containerUid, $areaUid, $destinationLanguageUid, $languageUid, $expectedResult)
    {
        $result = $this->subject->getRecordsToCopyDatabaseResult($containerUid, $areaUid, $destinationLanguageUid, $languageUid, 'uid');
        $result = $result->fetchAll();
        $this->assertEquals($expectedResult, $result);
    }

    public function getRecordsToCopyDatabaseResultDataProvider()
    {
        return [
            'from language 0 to 1 connected mode' => [
                1,
                0,
                1,
                0,
                'tx_grid_example_domain_model_container',
                'content',
                [
                    ['uid' => 298]
                ]
            ],
            'from language 1 to 2 connected mode' => [
                1,
                0,
                2,
                1,
                'tx_grid_example_domain_model_container',
                'content',
                [
                    ['uid' => 300]
                ]
            ],
            'from language 0 to 1 free mode' => [
                2,
                0,
                1,
                0,
                'tx_grid_example_domain_model_container',
                'content',
                []
            ],
            'from language 1 to 2 free mode' => [
                2,
                0,
                2,
                1,
                'tx_grid_example_domain_model_container',
                'content',
                []
            ],
            'from language 0 to 1 free mode copied' => [
                3,
                0,
                1,
                0,
                'tx_grid_example_domain_model_container',
                'content',
                []
            ],
            'from language 1 to 2 free mode  mode copied' => [
                3,
                0,
                2,
                1,
                'tx_grid_example_domain_model_container',
                'content',
                []
            ],
        ];
    }

    /**
     * @dataProvider getRecordsToCopyDatabaseResultDataProvider
     * @test
     */
    public function getRecordsToCopyDatabaseResult($containerUid, $areaUid, $destinationLanguageUid, $languageUid, $containerTable, $relationshipColumn, $expectedResult)
    {
        $result = $this->subject->getRecordsToCopyDatabaseResult($containerUid, $areaUid, $destinationLanguageUid, $languageUid, 'uid', $containerTable, $relationshipColumn);
        $result = $result->fetchAll();
        $this->assertEquals($expectedResult, $result);
    }
}

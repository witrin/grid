<?php
namespace TYPO3\CMS\Grid\Tests\Unit\Form\Data;

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

use Prophecy\Prophecy\ObjectProphecy;
use TYPO3\CMS\Backend\Configuration\TranslationConfigurationProvider;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Grid\Form\Data\LanguageOverlayProvider;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case
 */
class AdditionalLanguageProviderTest extends UnitTestCase
{
    /**
     * @var LanguageOverlayProvider|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $subject;

    protected function setUp()
    {
        $this->subject = $this->getMockBuilder(LanguageOverlayProvider::class)
            ->setMethods(['getRecordWorkspaceOverlay'])
            ->getMock();
    }

    /**
     * @test
     */
    public function addDataSetsAdditionalLanguageRowsIfRequested()
    {
        $input = [
            'tableName' => 'tt_content',
            'databaseRow' => [
                'uid' => 42,
                'title' => 'localized content',
                'sys_language_uid' => 2,
                'l10n_parent' => 23,
            ],
            'processedTca' => [
                'ctrl' => [
                    'languageField' => 'sys_language_uid',
                    'transOrigPointerField' => 'l10n_parent',
                ],
            ],
            'customData' => [
                'tx_grid' => [
                    'additionalLanguages' => [3],
                ],
            ],
            'systemLanguageRows' => [
                0 => [
                    'uid' => 0,
                    'title' => 'default'
                ],
                3 => [
                    'uid' => 3,
                    'title' => 'french'
                ],
            ],
            'additionalLanguageRows' => [],
        ];

        $translationResult = [
            'translations' => [
                3 => [
                    'uid' => 43,
                    'pid' => 32,
                ],
            ],
        ];
        // For BackendUtility::getRecord()
        $GLOBALS['TCA']['tt_content'] = ['foo'];
        $recordWorkspaceOverlayResult = [
            'uid' => 43,
            'pid' => 32,
            'text' => 'french content',
        ];

        /** @var TranslationConfigurationProvider|ObjectProphecy $translationProphecy */
        $translationProphecy = $this->prophesize(TranslationConfigurationProvider::class);
        GeneralUtility::addInstance(TranslationConfigurationProvider::class, $translationProphecy->reveal());
        $translationProphecy->translationInfo('tt_content', 23, 3)->shouldBeCalled()->willReturn($translationResult);

        $this->subject->expects($this->at(1))
            ->method('getRecordWorkspaceOverlay')
            ->with('tt_content', 43)
            ->willReturn($recordWorkspaceOverlayResult);

        $expected = $input;
        $expected['additionalLanguageRows'] = [
            3 => $recordWorkspaceOverlayResult
        ];

        $this->assertEquals($expected, $this->subject->addData($input));
    }
}

<?php

declare(strict_types=1);

namespace frontend\tests\unit\edu;

use Codeception\Test\Unit;
use frontend\tests\FunctionalTester;
use modules\edu\RequiredStory\repo\RequiredStoryMetadata;

class RequiredStoryMetadataTest extends Unit
{
    public function dataVariants()
    {
        return [
            'example1' => [
                'metadataTotal' => 92,
                'metadataChunks' => [
                    ['n' => 19],
                    ['n' => 19],
                    ['n' => 18],
                    ['n' => 18],
                    ['n' => 18],
                ],
                'expected' => 18,
                'total' => 92,
                'fact' => 39,
            ],
            'example2' => [
                'metadataTotal' => 4,
                'metadataChunks' => [
                    ['n' => 4],
                ],
                'expected' => 4,
                'total' => 59,
                'fact' => 55,
            ],
            'example3' => [
                'metadataTotal' => 35,
                'metadataChunks' => [
                    ['n' => 20],
                    ['n' => 10],
                    ['n' => 5],
                ],
                'expected' => 5,
                'total' => 100,
                'fact' => 99,
            ],
            'example4' => [
                'metadataTotal' => 96,
                'metadataChunks' => [
                    ['n' => 20],
                    ['n' => 19],
                    ['n' => 19],
                    ['n' => 19],
                    ['n' => 19],
                ],
                'expected' => 20,
                'total' => 96,
                'fact' => 0,
            ],
            'example5' => [
                'metadataTotal' => 96,
                'metadataChunks' => [
                    ['n' => 20],
                    ['n' => 19],
                    ['n' => 19],
                    ['n' => 19],
                    ['n' => 19],
                ],
                'expected' => 19,
                'total' => 96,
                'fact' => 21,
            ],
            'example6' => [
                'metadataTotal' => 96,
                'metadataChunks' => [
                    ['n' => 20],
                    ['n' => 19],
                    ['n' => 19],
                    ['n' => 19],
                    ['n' => 19],
                ],
                'expected' => 0,
                'total' => 96,
                'fact' => 96,
            ],
            'example7' => [
                'metadataTotal' => 5,
                'metadataChunks' => [
                    ['n' => 5],
                ],
                'expected' => 0,
                'total' => 96,
                'fact' => 96,
            ],
        ];
    }

    /**
     * @dataProvider dataVariants
     */
    public function testGetCurrentPlan(int $metadataTotal, array $metadataChunks, int $expected, int $total, int $fact): void
    {
        $metadata = new RequiredStoryMetadata(
            $metadataTotal,
            $metadataChunks,
        );
        $this->assertEquals(
            $expected,
            $metadata->getCurrentPlan($total, $fact)
        );
    }
}

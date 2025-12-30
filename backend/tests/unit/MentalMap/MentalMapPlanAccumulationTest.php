<?php

declare(strict_types=1);

namespace backend\tests\unit\MentalMap;

use backend\MentalMap\MentalMapPayload;
use Codeception\Test\Unit;

class MentalMapPlanAccumulationTest extends Unit
{
    public function testSuccess(): void
    {
        $fragments = [
            [
                'id' => '111',
                'title' => 'Fragment 1 title',
                'description' => 'Fragment 1 description',
            ],
            [
                'id' => '222',
                'title' => 'Fragment 2 title',
                'description' => 'Fragment 2 description',
            ],
            [
                'id' => '333',
                'title' => 'Fragment 3 title',
                'description' => 'Fragment 3 description',
            ],
        ];
        $this->assertEquals(
            [
                [
                    "id" => "111",
                    "title" => "Fragment 1 title",
                    "description" => "Fragment 1 description",
                ],
                [
                    "id" => "222",
                    "title" => "Fragment 1 title\r\nFragment 2 title",
                    "description" => "Fragment 1 description\r\nFragment 2 description",
                ],
                [
                    "id" => "333",
                    "title" => "Fragment 1 title\r\nFragment 2 title\r\nFragment 3 title",
                    "description" => "Fragment 1 description\r\nFragment 2 description\r\nFragment 3 description",
                ],
            ],
            MentalMapPayload::accumulateFragments($fragments),
        );
    }

    public function testSuccessOne(): void
    {
        $fragments = [
            [
                'id' => '111',
                'title' => 'Fragment 1 title',
                'description' => 'Fragment 1 description',
            ],
        ];
        $this->assertEquals(
            [
                [
                    "id" => "111",
                    "title" => "Fragment 1 title",
                    "description" => "Fragment 1 description",
                ],
            ],
            MentalMapPayload::accumulateFragments($fragments),
        );
    }
}

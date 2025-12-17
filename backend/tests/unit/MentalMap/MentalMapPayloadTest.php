<?php

declare(strict_types=1);

namespace backend\tests\unit\MentalMap;

use backend\MentalMap\MentalMapPayload;
use Codeception\Test\Unit;

class MentalMapPayloadTest extends Unit
{
    public function testFilterFragments(): void
    {
        $fragments = [
            [
                'id' => '111',
                'title' => 'Fragment 1 title',
            ],
            [
                'id' => '222',
                'title' => 'Fragment 2 title',
            ],
            [
                'id' => '333',
                'title' => 'Fragment 3 title',
            ],
        ];
        $this->assertCount(3, MentalMapPayload::filterEmptyFragments($fragments));
    }

    public function testFilterFragmentsWithEmpty(): void
    {
        $fragments = [
            [
                'id' => '111',
                'title' => 'Fragment 1 title',
            ],
            [
                'id' => '222',
                'title' => '',
            ],
            [
                'id' => '333',
                'title' => 'Fragment 3 title',
            ],
        ];
        $this->assertCount(2, MentalMapPayload::filterEmptyFragments($fragments));
    }

    public function testFilterFragmentsTree(): void
    {
        $fragments = [
            [
                'id' => '111',
                'title' => 'Fragment 1 title',
                'children' => [
                    'id' => '777',
                    'title' => 'Children title 1',
                    'children' => [],
                    'expanded' => true,
                    'description' => 'description',
                ],
                'expanded' => true,
                'description' => 'description',
            ],
            [
                'id' => '222',
                'title' => 'Fragment 2 title',
            ],
            [
                'id' => '333',
                'title' => 'Fragment 3 title',
            ],
        ];
        $this->assertCount(3, MentalMapPayload::filterEmptyFragments($fragments));
    }

    public function testFilterFragmentsTreeWithEmpty(): void
    {
        $fragments = [
            [
                'id' => '111',
                'title' => 'Fragment 1 title',
                'children' => [
                    'id' => '777',
                    'title' => 'Children title 1',
                    'children' => [],
                    'expanded' => true,
                    'description' => 'description',
                ],
                'expanded' => true,
                'description' => 'description',
            ],
            [
                'id' => '222',
                'title' => 'Fragment 2 title',
            ],
            [
                'id' => '333',
                'title' => '',
            ],
        ];
        $this->assertCount(2, MentalMapPayload::filterEmptyFragments($fragments));
    }
}

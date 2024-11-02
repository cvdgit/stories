<?php

declare(strict_types=1);

namespace frontend\tests\unit;

use frontend\MentalMap\MentalMap;

class MentalMapTest extends \Codeception\Test\Unit
{
    public function testIsDoneEmpty(): void
    {
        $this->assertFalse(MentalMap::isDone([]));
    }

    public function testIsDoneSuccess(): void
    {
        $history = [
            [
                'id' => '111',
                'all' => 10,
                'hiding' => 0,
                'target' => 0,
            ],
            [
                'id' => '222',
                'all' => 20,
                'hiding' => 0,
                'target' => 0,
            ],
            [
                'id' => '333',
                'all' => 30,
                'hiding' => 0,
                'target' => 0,
            ],
        ];
        $this->assertTrue(MentalMap::isDone($history));
    }

    public function testIsDoneFailed(): void
    {
        $history = [
            [
                'id' => '111',
                'all' => 10,
                'hiding' => 0,
                'target' => 0,
            ],
            [
                'id' => '222',
                'all' => 0,
                'hiding' => 0,
                'target' => 0,
            ],
            [
                'id' => '333',
                'all' => 30,
                'hiding' => 0,
                'target' => 0,
            ],
        ];
        $this->assertFalse(MentalMap::isDone($history));
    }
}

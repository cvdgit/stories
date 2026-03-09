<?php

declare(strict_types=1);

namespace frontend\tests\unit\edu;

use Codeception\Test\Unit;
use modules\edu\RequiredStory\repo\RequiredStoryMetadata;

class RequiredStoryMetadataTest extends Unit
{
    public function testGetCurrentPlan(): void
    {
        $metadata = new RequiredStoryMetadata(
            92,
            [
                ['n' => 19],
                ['n' => 19],
                ['n' => 18],
                ['n' => 18],
                ['n' => 18],
            ],
        );
        $this->assertEquals(
            18,
            $metadata->getCurrentPlan(39)
        );
    }
}

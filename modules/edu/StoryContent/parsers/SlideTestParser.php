<?php

declare(strict_types=1);

namespace modules\edu\StoryContent\parsers;

use common\models\StoryTest;
use DomainException;
use modules\edu\query\GetStoryTests\SlideTest;

class SlideTestParser implements ContentParseInterface {

    /**
     * @var SlideTest
     */
    private $contentItem;

    public function __construct(SlideTest $contentItem) {
        $this->contentItem = $contentItem;
    }

    public function parse(): int
    {
        $test = StoryTest::findOne($this->contentItem->getTestId());
        if ($test === null) {
            throw new DomainException('Test not found');
        }
        return $test->calculateNumberOfQuestions();
    }
}

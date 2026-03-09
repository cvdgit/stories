<?php

declare(strict_types=1);

namespace modules\edu\StoryContent\parsers;

use backend\MentalMap\MentalMap;
use DomainException;
use modules\edu\query\GetStoryTests\SlideMentalMap;

class SlideMentalMapParser implements ContentParseInterface
{
    /**
     * @var SlideMentalMap
     */
    private $contentItem;

    public function __construct(SlideMentalMap $contentItem)
    {
        $this->contentItem = $contentItem;
    }

    public function parse(): int
    {
        $mentalMap = MentalMap::findOne($this->contentItem->getMentalMapId());
        if ($mentalMap === null) {
            throw new DomainException('Mental Map not found');
        }
        return count($mentalMap->getItems());
    }
}

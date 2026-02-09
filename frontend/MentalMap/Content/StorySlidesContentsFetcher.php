<?php

declare(strict_types=1);

namespace frontend\MentalMap\Content;

use frontend\MentalMap\MentalMapStorySlide;
use Ramsey\Uuid\Uuid;

class StorySlidesContentsFetcher
{
    /**
     * @param array<array-key, int> $slideIds
     * @return SlideContentItem[]
     */
    public function fetch(array $slideIds, bool $required): array
    {
        $rows = MentalMapStorySlide::findAllBySlideIds($slideIds, $required);
        return array_map(
            static function (MentalMapStorySlide $row): SlideContentItem {
                return new SlideContentItem(
                    $row->slide_id,
                    Uuid::fromString($row->mental_map_id),
                    $row->block_id,
                    $row->getRequired(),
                );
            },
            $rows,
        );
    }
}

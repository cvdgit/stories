<?php

declare(strict_types=1);

namespace backend\components\story;

use backend\components\story\reader\HTMLReader;

class StoryMentalMapFetcher
{
    /**
     * @param string $storyData
     * @return array<array-key, string>
     */
    public function fetch(string $storyData): array {
        $story = (new HTMLReader($storyData))->load();
        $mentalMapIds = [];
        foreach ($story->getSlides() as $slide) {
            foreach ($slide->getBlocks() as $block) {
                if ($block->isMentalMap()) {
                    /** @var $block MentalMapBlock */
                    $content = $block->getContent();
                    $fragment = \phpQuery::newDocumentHTML($content);
                    $mentalMapId = $fragment->find('.mental-map')->attr('data-mental-map-id');
                    $mentalMapIds[] = $mentalMapId;
                }
            }
        }
        return $mentalMapIds;
    }
}

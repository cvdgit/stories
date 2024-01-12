<?php

declare(strict_types=1);

namespace backend\Story\Tests;

use backend\components\story\HTMLBLock;
use backend\components\story\reader\HTMLReader;
use backend\components\story\TestBlock;

class StorySourceTestsFetcher
{
    public function fetch(string $source): array
    {
        $story =( new HTMLReader($source))->load();
        $testIds = [];
        foreach ($story->getSlides() as $slide) {
            foreach ($slide->getBlocks() as $block) {
                if ($block->isTest()) {
                    /** @var $block TestBlock */
                    $testIds[] = $block->getTestId();
                }
                if ($block->isHtmlTest()) {
                    /** @var $block HTMLBLock */
                    $content = $block->getContent();
                    $fragment = \phpQuery::newDocumentHTML($content);
                    $testId = $fragment->find('.new-questions')->attr('data-test-id');
                    $testIds[] = $testId;
                }
            }
        }
        return $testIds;
    }
}

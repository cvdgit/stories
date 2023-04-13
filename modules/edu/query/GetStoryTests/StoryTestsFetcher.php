<?php

declare(strict_types=1);

namespace modules\edu\query\GetStoryTests;

use backend\components\story\AbstractBlock;
use backend\components\story\HTMLBLock;
use backend\components\story\reader\HTMLReader;
use backend\components\story\Slide;
use backend\components\story\TestBlock;
use common\models\Story;
use yii\web\NotFoundHttpException;

class StoryTestsFetcher
{
    /**
     * @throws NotFoundHttpException
     */
    public function fetch(int $storyId): array
    {
        $story = Story::findOne($storyId);
        if ($story === null) {
            throw new NotFoundHttpException('История не найдена');
        }

        $data = [];

        $story = (new HTMLReader($story->slidesData()))->load();

        foreach ($story->getSlides() as $slide) {
            /** @var Slide $slide */

            foreach ($slide->getBlocks() as $block) {

                if ($block->getType() === AbstractBlock::TYPE_HTML) {
                    /** @var $block HTMLBLock */
                    $content = $block->getContent();
                    $fragment = \phpQuery::newDocumentHTML($content);
                    $testId = $fragment->find('.new-questions')->attr('data-test-id');
                    if ($testId !== null) {
                        $data[] = new SlideTest((int) $slide->getId(), (int) $slide->getSlideNumber(), (int) $testId);
                    }
                }

                if ($block->getType() === AbstractBlock::TYPE_TEST) {
                    /** @var $block TestBlock */
                    $testId = $block->getTestID();
                    if ($testId !== null) {
                        $data[] = new SlideTest((int) $slide->getId(), (int) $slide->getSlideNumber(), (int) $testId);
                    }
                }
            }
        }

        return $data;
    }
}

<?php

declare(strict_types=1);

namespace modules\edu\query\GetStoryTests;

use backend\components\story\AbstractBlock;
use backend\components\story\HTMLBLock;
use backend\components\story\MentalMapBlockContent;
use backend\components\story\reader\HTMLReader;
use backend\components\story\TestBlock;
use common\models\Story;
use yii\base\InvalidConfigException;
use yii\web\NotFoundHttpException;

class StoryTestsFetcher
{

    /**
     * @throws NotFoundHttpException
     * @throws InvalidConfigException
     */
    public function fetch(int $storyId): SlideContentCollection
    {
        $story = Story::findOne($storyId);
        if ($story === null) {
            throw new NotFoundHttpException('История не найдена');
        }

        $story = (new HTMLReader($story->slidesData()))->load();

        $data = [];
        foreach ($story->getSlides() as $slide) {
            foreach ($slide->getBlocks() as $block) {
                if ($block->getType() === AbstractBlock::TYPE_HTML) {
                    /** @var $block HTMLBLock */
                    $content = $block->getContent();
                    $fragment = \phpQuery::newDocumentHTML($content);
                    $testId = $fragment->find('.new-questions')->attr('data-test-id');
                    if ($testId !== null) {
                        $data[] = new SlideTest($slide->getId(), (int) $slide->getSlideNumber(), (int) $testId);
                    }
                }

                if ($block->getType() === AbstractBlock::TYPE_TEST) {
                    /** @var $block TestBlock */
                    $testId = $block->getTestID();
                    if ($testId !== null) {
                        $data[] = new SlideTest($slide->getId(), (int) $slide->getSlideNumber(), (int) $testId);
                    }
                }

                if ($block->getType() === AbstractBlock::TYPE_MENTAL_MAP) {
                    $content = MentalMapBlockContent::createFromHtml($block->getContent());
                    $data[] = new SlideMentalMap($slide->getId(), (int) $slide->getSlideNumber(), $content->getId());
                }
            }
        }

        return new SlideContentCollection($data);
    }
}

<?php

declare(strict_types=1);

namespace backend\actions\SlideImport;

use backend\components\SlideModifier;
use backend\components\story\HTMLBLock;
use backend\components\story\ImageBlock;
use backend\components\story\reader\HtmlSlideReader;
use backend\components\story\TestBlock;
use backend\components\story\TestBlockContent;
use backend\models\ImageSlideBlock;
use backend\services\StoryEditorService;
use common\models\Story;
use common\models\StorySlide;
use common\models\StoryStoryTest;
use common\services\TransactionManager;
use yii\web\NotFoundHttpException;

class ImportHandler
{
    /** @var TransactionManager */
    private $transactionManager;

    private $editorService;

    public function __construct(TransactionManager $transactionManager, StoryEditorService $editorService)
    {
        $this->transactionManager = $transactionManager;
        $this->editorService = $editorService;
    }

    /**
     * @throws NotFoundHttpException
     */
    public function handle(SlideImportForm $command): void
    {
        $fromStory = Story::findOne($command->from_story_id);
        if ($fromStory === null) {
            throw new NotFoundHttpException('История не найдена');
        }
        $toStory = Story::findOne($command->to_story_id);
        if ($toStory === null) {
            throw new NotFoundHttpException('История не найдена');
        }

        $this->transactionManager->wrap(function() use ($command, $fromStory, $toStory) {

            foreach ($command->slides as $slideId) {

                $fromSlide = StorySlide::findStorySlide($fromStory->id, (int)$slideId);
                if ($fromSlide === null) {
                    throw new NotFoundHttpException('Слайд не найден');
                }

                $newSlide = $fromSlide->copySlide($fromSlide, $toStory->id);
                if (!$newSlide->save()) {
                    throw new \DomainException('Copy slide error');
                }

                $newSlide->data = (new SlideModifier($newSlide->id, $newSlide->data))
                    ->addImageId()
                    ->render();
                $newSlide->update(false, ['data']);

                $this->createSlideRelations($newSlide);
            }
        });

        if ((bool)$command->delete_slides) {
            $this->transactionManager->wrap(function() use ($command, $fromStory) {
                foreach ($command->slides as $slideId) {
                    $fromSlide = StorySlide::findStorySlide($fromStory->id, (int)$slideId);
                    if ($fromSlide === null) {
                        throw new NotFoundHttpException('Слайд не найден');
                    }
                    $this->editorService->deleteSlide($fromSlide);
                }
            });
        }
    }

    private function createSlideRelations(StorySlide $slideModel): void
    {
        $reader = new HtmlSlideReader($slideModel->data);
        $slide = $reader->load();

        foreach ($slide->getBlocks() as $block) {
            if ($block->isImage()) {
                /** @var $block ImageBlock */
                $imageId = $block->getBlockAttribute('data-image-id');
                if ($imageId !== null) {
                    $image = ImageSlideBlock::create((int)$imageId, $slideModel->id, $block->getId());
                    $image->save();
                }
            } elseif ($block->isHtmlTest()) {
                /** @var HTMLBLock $block */
                /** @var TestBlockContent $content */
                $content = $block->getContentObject(TestBlockContent::class);
                $model = StoryStoryTest::create($slideModel->story_id, (int)$content->getTestID());
                $model->save();
            } elseif ($block->isTest()) {
                /** @var TestBlock $block */
                $model = StoryStoryTest::create($slideModel->story_id, (int)$block->getTestID());
                $model->save();
            }
        }
    }
}

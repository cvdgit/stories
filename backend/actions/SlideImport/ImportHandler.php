<?php

declare(strict_types=1);

namespace backend\actions\SlideImport;

use backend\components\SlideModifier;
use backend\components\story\ImageBlock;
use backend\components\story\MentalMapBlockContent;
use backend\components\story\reader\HtmlSlideReader;
use backend\components\story\RetellingBlockContent;
use backend\components\story\TestBlock;
use backend\components\story\TestBlockContent;
use backend\MentalMap\MentalMap;
use backend\models\ImageSlideBlock;
use backend\Retelling\Retelling;
use backend\services\StoryEditorService;
use backend\services\StorySlideService;
use backend\SlideEditor\CopyMentalMap\CopyMentalMapCommand;
use backend\SlideEditor\CopyMentalMap\CopyMentalMapHandler;
use backend\Testing\ImportQuestions\Import\ImportCommand;
use common\models\Story;
use common\models\StorySlide;
use common\models\StoryStoryTest;
use common\models\StoryTest;
use common\models\StoryTestQuestion;
use common\services\TransactionManager;
use DomainException;
use Exception;
use modules\edu\query\GetStoryTests\SlideMentalMap;
use modules\edu\query\GetStoryTests\StoryTestsFetcher;
use Ramsey\Uuid\Uuid;
use yii\web\NotFoundHttpException;

class ImportHandler
{
    /** @var TransactionManager */
    private $transactionManager;

    private $editorService;
    /**
     * @var StorySlideService
     */
    private $storySlideService;
    /**
     * @var StoryEditorService
     */
    private $storyEditorService;
    /**
     * @var CopyMentalMapHandler
     */
    private $copyMentalMapHandler;
    /**
     * @var \backend\Testing\ImportQuestions\Import\ImportHandler
     */
    private $quizImportHandler;

    public function __construct(
        TransactionManager $transactionManager,
        StoryEditorService $editorService,
        StorySlideService $storySlideService,
        StoryEditorService $storyEditorService,
        CopyMentalMapHandler $copyMentalMapHandler,
        \backend\Testing\ImportQuestions\Import\ImportHandler $quizImportHandler
    ) {
        $this->transactionManager = $transactionManager;
        $this->editorService = $editorService;
        $this->storySlideService = $storySlideService;
        $this->storyEditorService = $storyEditorService;
        $this->copyMentalMapHandler = $copyMentalMapHandler;
        $this->quizImportHandler = $quizImportHandler;
    }

    /**
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function handle(SlidesImportCommand $command): void
    {
        if (count($command->getSlides()) === 0) {
            throw new DomainException('No slides to import');
        }

        $fromStory = Story::findOne($command->getFromStoryId());
        if ($fromStory === null) {
            throw new NotFoundHttpException('История не найдена');
        }
        $toStory = Story::findOne($command->getToStoryId());
        if ($toStory === null) {
            throw new NotFoundHttpException('История не найдена');
        }

        $slideNumber = $this->getStartSlideNumber(
            $toStory,
            $command->getInsertAfterSlideId(),
        );

        $contents = (new StoryTestsFetcher())->fetch($fromStory->id);
        $mentalMapSlideIds = array_map(static function (SlideMentalMap $slideMentalMap) {
            return $slideMentalMap->getSlideId();
        }, $contents->find(SlideMentalMap::class));

        $this->transactionManager->wrap(function () use ($command, $fromStory, $toStory, $slideNumber, $mentalMapSlideIds) {
            foreach ($command->getSlides() as $slideId) {
                $fromSlide = StorySlide::findStorySlide($fromStory->id, (int) $slideId);
                if ($fromSlide === null) {
                    throw new NotFoundHttpException('Слайд не найден');
                }

                if ($fromSlide->kind === StorySlide::KIND_MENTAL_MAP && in_array($fromSlide->id, $mentalMapSlideIds, true)) {
                    $this->copyMentalMapSlide(
                        $toStory->id,
                        $slideNumber,
                        $fromSlide->getSlideOrLinkData(),
                        $command->getUserId(),
                    );
                    $slideNumber++;
                    continue;
                }

                if ($fromSlide->kind === StorySlide::KIND_RETELLING) {
                    $this->copyRetellingSlide(
                        $toStory->id,
                        $slideNumber,
                        $fromSlide->getSlideOrLinkData(),
                        $command->getUserId(),
                        $fromStory->id,
                    );
                    $slideNumber += 2;
                    continue;
                }

                if ($fromSlide->kind === StorySlide::KIND_QUESTION) {
                    $this->copyQuizSlide(
                        $toStory->id,
                        $slideNumber,
                        $fromSlide->getSlideOrLinkData(),
                        $command->getUserId(),
                    );
                    $slideNumber++;
                    continue;
                }

                $newSlide = $this->storySlideService->createSlide(
                    $toStory->id,
                    $fromSlide->kind,
                    $slideNumber,
                    function (int $slideId) use ($fromSlide): string {
                        return (new SlideModifier($slideId, $fromSlide->getSlideOrLinkData()))
                            ->addImageId()
                            ->withEmptyTableOfContents()
                            ->render();
                    },
                );
                $this->createSlideRelations($newSlide);
                $slideNumber++;
            }
        });

        if ($command->isDeleteSlidesAfterImport()) {
            $this->transactionManager->wrap(function () use ($command, $fromStory) {
                foreach ($command->getSlides() as $slideId) {
                    $fromSlide = StorySlide::findStorySlide($fromStory->id, (int) $slideId);
                    if ($fromSlide === null) {
                        throw new NotFoundHttpException('Слайд не найден');
                    }
                    $this->editorService->deleteSlide($fromSlide);
                }
            });
        }
    }

    private function getStartSlideNumber(Story $toStory, int $insertAfterSlideId = null): int
    {
        if ($insertAfterSlideId === null) {
            $slideNumber = (int) $toStory->getStorySlides()->max('number');
            return $slideNumber + 1;
        }

        $slide = StorySlide::findOne($insertAfterSlideId);
        if ($slide === null) {
            throw new DomainException('Insert after slide not found');
        }
        return $slide->number + 1;
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
                    $image = ImageSlideBlock::create((int) $imageId, $slideModel->id, $block->getId());
                    $image->save();
                }
                continue;
            }
            if ($block->isTest()) {
                /** @var $block TestBlock */
                $model = StoryStoryTest::create($slideModel->story_id, (int) $block->getTestID());
                $model->save();
            }
        }
    }

    /**
     * @throws Exception
     */
    private function copyMentalMapSlide(int $storyId, int $slideNumber, string $slideData, int $userId): void
    {
        $content = MentalMapBlockContent::createFromHtml($slideData);

        $mentalMap = MentalMap::findOne($content->getId());
        if ($mentalMap === null) {
            throw new DomainException('Mental Map not found');
        }

        $newMentalMapId = Uuid::uuid4();
        $this->copyMentalMapHandler->handle(
            new CopyMentalMapCommand(
                $newMentalMapId,
                Uuid::fromString($content->getId()),
                $mentalMap->name,
                $userId,
            ),
        );

        $this->storySlideService->createSlide(
            $storyId,
            StorySlide::KIND_MENTAL_MAP,
            $slideNumber,
            function (int $slideId) use ($content, $newMentalMapId): string {
                return $this->storyEditorService->getSlideWithMentalMapBlockContent(
                    $slideId,
                    $newMentalMapId->toString(),
                    $content->getMapType(),
                    $content->isRequired(),
                );
            },
        );
    }

    /**
     * @throws Exception
     */
    private function copyRetellingSlide(int $storyId, int $slideNumber, string $slideData, int $userId, int $fromStoryId): void
    {
        $content = RetellingBlockContent::createFromHtml($slideData);

        $retelling = Retelling::findOne($content->getId());
        if ($retelling === null) {
            throw new DomainException('Retelling not found');
        }

        $retellingSlide = StorySlide::findStorySlide($fromStoryId, $retelling->slide_id);
        if ($retellingSlide === null) {
            throw new DomainException('Retelling slide not found');
        }

        $newRetellingSlide = $this->storySlideService->createSlide(
            $storyId,
            $retellingSlide->kind,
            $slideNumber,
            function (int $slideId) use ($retellingSlide): string {
                return (new SlideModifier($slideId, $retellingSlide->getSlideOrLinkData()))
                    ->addImageId()
                    ->render();
            },
        );

        $slideNumber++;

        $newRetellingId = Uuid::uuid4();
        $newRetelling = Retelling::copyRetelling(
            Uuid::fromString($content->getId()),
            $newRetellingId,
            $retelling->name,
            $userId,
        );
        $newRetelling->slide_id = $newRetellingSlide->id;
        if (!$newRetelling->save()) {
            throw new DomainException('New Retelling Save Exception');
        }

        $this->storySlideService->createSlide(
            $storyId,
            StorySlide::KIND_RETELLING,
            $slideNumber,
            function (int $slideId) use ($content, $newRetellingId): string {
                return $this->storyEditorService->getSlideWithRetellingBlockContent(
                    $slideId,
                    $newRetellingId->toString(),
                    $content->isRequired(),
                );
            },
        );
    }

    /**
     * @throws Exception
     */
    private function copyQuizSlide(int $storyId, int $slideNumber, string $slideData, int $userId): void
    {
        $content = TestBlockContent::createFromHtml($slideData);

        $quiz = StoryTest::findOne($content->getTestID());
        if ($quiz === null) {
            throw new DomainException('Test not found');
        }

        $quizQuestionIds = array_map(static function(StoryTestQuestion $question) {
            return $question->id;
        }, $quiz->storyTestQuestions);

        $newQuiz = $quiz->makeCopy($userId);
        if (!$newQuiz->save()) {
            throw new DomainException('Copy test save error');
        }

        $quizStoryRelation = StoryStoryTest::create($storyId, $newQuiz->id);
        if (!$quizStoryRelation->save()) {
            throw new DomainException('Quiz - Story relations create error');
        }

        $this->quizImportHandler->handle(
            new ImportCommand(
                $quiz->id,
                $newQuizId = $newQuiz->id,
                $quizQuestionIds,
            ),
        );

        $this->storySlideService->createSlide(
            $storyId,
            StorySlide::KIND_QUESTION,
            $slideNumber,
            function (int $slideId) use ($newQuizId): string {
                return $this->storyEditorService->createQuestionBlock(
                    $slideId,
                    $newQuizId,
                );
            },
        );
    }
}

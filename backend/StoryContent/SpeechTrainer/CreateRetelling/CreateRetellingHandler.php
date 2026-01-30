<?php

declare(strict_types=1);

namespace backend\StoryContent\SpeechTrainer\CreateRetelling;

use backend\Retelling\Retelling;
use backend\services\StoryEditorService;
use backend\services\StorySlideService;
use common\models\StorySlide;
use common\services\TransactionManager;
use DomainException;
use Ramsey\Uuid\Uuid;

class CreateRetellingHandler
{
    /**
     * @var TransactionManager
     */
    private $transactionManager;
    /**
     * @var StoryEditorService
     */
    private $storyEditorService;
    /**
     * @var StorySlideService
     */
    private $storySlideService;

    public function __construct(
        TransactionManager $transactionManager,
        StoryEditorService $storyEditorService,
        StorySlideService $storySlideService
    ) {
        $this->transactionManager = $transactionManager;
        $this->storyEditorService = $storyEditorService;
        $this->storySlideService = $storySlideService;
    }

    public function handle(CreateRetellingCommand $command): int
    {
        $retelling = Retelling::create(
            Uuid::uuid4(),
            $command->getCurrentSlideId(),
            'Перескажите текст',
            '',
            false,
            $command->getUserId(),
        );
        $retellingSlideId = null;
        $this->transactionManager->wrap(
            function () use ($retelling, $command, &$retellingSlideId): void {
                if (!$retelling->save()) {
                    throw new DomainException('Retelling save error');
                }
                $retellingSlide = $this->storySlideService->createAndInsertSlide(
                    $command->getStoryId(),
                    StorySlide::KIND_RETELLING,
                    $command->getCurrentSlideNumber(),
                    function (int $slideId) use ($retelling, $command): string {
                        return $this->storyEditorService->getSlideWithRetellingBlockContent(
                            $slideId,
                            $retelling->id,
                            $command->isRequired(),
                        );
                    },
                );
                $retellingSlideId = $retellingSlide->id;
            },
        );
        return $retellingSlideId;
    }
}

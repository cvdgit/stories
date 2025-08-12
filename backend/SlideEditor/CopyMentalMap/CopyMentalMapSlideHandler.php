<?php

declare(strict_types=1);

namespace backend\SlideEditor\CopyMentalMap;

use backend\services\StoryEditorService;
use backend\services\StorySlideService;
use common\models\Story;
use common\models\StorySlide;
use common\services\TransactionManager;
use DomainException;
use Exception;
use Ramsey\Uuid\Uuid;
use yii\web\NotFoundHttpException;

class CopyMentalMapSlideHandler
{
    /**
     * @var TransactionManager
     */
    private $transactionManager;
    /**
     * @var StorySlideService
     */
    private $storySlideService;
    /**
     * @var CopyMentalMapHandler
     */
    private $copyMentalMapHandler;
    /**
     * @var StoryEditorService
     */
    private $storyEditorService;

    public function __construct(
        TransactionManager $transactionManager,
        StorySlideService $storySlideService,
        CopyMentalMapHandler $copyMentalMapHandler,
        StoryEditorService $storyEditorService
    ) {
        $this->transactionManager = $transactionManager;
        $this->storySlideService = $storySlideService;
        $this->copyMentalMapHandler = $copyMentalMapHandler;
        $this->storyEditorService = $storyEditorService;
    }

    /**
     * @throws Exception
     */
    public function handle(CopyMentalMapSlideCommand $command): int
    {
        $currentSlideModel = StorySlide::findOne($command->getCurrentSlideId());
        if ($currentSlideModel === null) {
            throw new NotFoundHttpException('Слайд не найден');
        }
        $newSlideId = null;
        $this->transactionManager->wrap(function () use ($command, $currentSlideModel, &$newSlideId) {
            $slideModel = $this->storySlideService->create(
                $command->getStoryId(),
                'empty',
                StorySlide::KIND_MENTAL_MAP,
            );
            $slideModel->number = $currentSlideModel->number + 1;
            Story::insertSlideNumber($command->getStoryId(), $currentSlideModel->number);
            if (!$slideModel->save()) {
                throw new DomainException(
                    'Can\'t be saved StorySlide model. Errors: ' . implode(', ', $slideModel->getFirstErrors()),
                );
            }

            $newMentalMapId = Uuid::uuid4();
            $this->copyMentalMapHandler->handle(
                new CopyMentalMapCommand(
                    $newMentalMapId,
                    $command->getMentalMapCopyId(),
                    $command->getMentalMapCopyName(),
                    $command->getUserId(),
                ),
            );

            $data = $this->storyEditorService->getSlideWithMentalMapBlockContent(
                $slideModel->id,
                $newMentalMapId->toString(),
                'mental-map',
                $command->isMentalMapRequired(),
            );
            $slideModel->updateData($data);
            if (!$slideModel->save()) {
                throw new DomainException(
                    'Can\'t be saved StorySlide model. Errors: ' . implode(', ', $slideModel->getFirstErrors()),
                );
            }
            $newSlideId = $slideModel->id;
        });
        return $newSlideId;
    }
}

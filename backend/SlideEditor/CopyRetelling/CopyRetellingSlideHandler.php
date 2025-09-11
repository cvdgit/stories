<?php

declare(strict_types=1);

namespace backend\SlideEditor\CopyRetelling;

use backend\Retelling\Retelling;
use backend\services\StoryEditorService;
use backend\services\StorySlideService;
use common\models\Story;
use common\models\StorySlide;
use common\services\TransactionManager;
use DomainException;
use Exception;
use Ramsey\Uuid\Uuid;
use yii\web\NotFoundHttpException;

class CopyRetellingSlideHandler
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
     * @var StoryEditorService
     */
    private $storyEditorService;

    public function __construct(
        TransactionManager $transactionManager,
        StorySlideService $storySlideService,
        StoryEditorService $storyEditorService
    ) {
        $this->transactionManager = $transactionManager;
        $this->storySlideService = $storySlideService;
        $this->storyEditorService = $storyEditorService;
    }

    /**
     * @throws Exception
     */
    public function handle(CopyRetellingSlideCommand $command): int
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
                StorySlide::KIND_RETELLING,
            );
            $slideModel->number = $currentSlideModel->number + 1;
            Story::insertSlideNumber($command->getStoryId(), $currentSlideModel->number);
            if (!$slideModel->save()) {
                throw new DomainException(
                    'Can\'t be saved StorySlide model. Errors: ' . implode(', ', $slideModel->getFirstErrors()),
                );
            }

            $newRetellingId = Uuid::uuid4();
            $newRetelling = Retelling::copyRetelling(
                $command->getRetellingCopyId(),
                $newRetellingId,
                $command->getRetellingName(),
                $command->getUserId(),
            );
            if (!$newRetelling->save()) {
                throw new DomainException('New Retelling Save Exception');
            }

            $data = $this->storyEditorService->getSlideWithRetellingBlockContent(
                $slideModel->id,
                $newRetellingId->toString(),
                $command->isRetellingRequired(),
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

<?php

declare(strict_types=1);

namespace backend\services;

use common\models\Story;
use common\models\StorySlide;
use common\services\TransactionManager;
use DomainException;
use Exception;

class StorySlideService
{
    /**
     * @var TransactionManager
     */
    private $transactionManager;

    public function __construct(TransactionManager $transactionManager)
    {
        $this->transactionManager = $transactionManager;
    }

    public function create(int $storyID, string $data, int $kind): StorySlide
    {
        $model = StorySlide::createSlide($storyID);
        $model->data = $data;
        $model->kind = $kind;
        return $model;
    }

    /**
     * @throws Exception
     */
    public function createAndInsertSlide(int $storyId, int $kind, int $afterSlideNumber, callable $setData): StorySlide
    {
        $slide = StorySlide::createSlide($storyId);
        $slide->data = 'empty';
        $slide->kind = $kind;
        $slide->number = $afterSlideNumber + 1;

        $this->transactionManager->wrap(static function () use ($slide, $storyId, $afterSlideNumber, $setData): void {
            Story::insertSlideNumber($storyId, $afterSlideNumber);

            if (!$slide->save()) {
                throw new DomainException(
                    'Can\'t be saved Story model. Errors: ' . implode(', ', $slide->getFirstErrors()),
                );
            }

            $slide->data = $setData($slide->id);

            if (!$slide->save()) {
                throw new DomainException(
                    'Can\'t be saved Story model. Errors: ' . implode(', ', $slide->getFirstErrors()),
                );
            }
        });

        return $slide;
    }
}

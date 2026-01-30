<?php

declare(strict_types=1);

namespace backend\StoryContent\SpeechTrainer;

use backend\MentalMap\MentalMapStorySlide;
use backend\StoryContent\SpeechTrainer\CreateRetelling\CreateRetellingCommand;
use backend\StoryContent\SpeechTrainer\CreateRetelling\CreateRetellingHandler;
use DomainException;
use Ramsey\Uuid\UuidInterface;

class SpeechTrainerService
{
    /**
     * @var CreateRetellingHandler
     */
    private $createRetellingHandler;

    public function __construct(CreateRetellingHandler $createRetellingHandler)
    {
        $this->createRetellingHandler = $createRetellingHandler;
    }

    public function createRetelling(
        int $storyId,
        int $currentSlideId,
        int $currentSlideNumber,
        int $userId,
        bool $required = false
    ): int {
        return $this->createRetellingHandler->handle(
            new CreateRetellingCommand(
                $storyId,
                $currentSlideId,
                $currentSlideNumber,
                $userId,
                $required,
            ),
        );
    }

    public function createMentalMapSlideRow(
        UuidInterface $mentalMapId,
        int $slideId,
        string $blockId,
        bool $required = false
    ): void {
        $mentalMapSlideRow = MentalMapStorySlide::create(
            $mentalMapId->toString(),
            $slideId,
            $blockId,
            $required,
        );
        if (!$mentalMapSlideRow->save()) {
            throw new DomainException('Mental Map Story Slide save exception');
        }
    }
}

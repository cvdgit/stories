<?php

declare(strict_types=1);

namespace modules\edu\RequiredStory\Create;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use modules\edu\RequiredStory\repo\RequiredStoriesRepository;
use modules\edu\RequiredStory\repo\RequiredStory;
use modules\edu\RequiredStory\repo\RequiredStoryStatus;

class CreateRequiredStoryHandler
{
    /**
     * @var RequiredStoriesRepository
     */
    private $requiredStoriesRepository;

    public function __construct(RequiredStoriesRepository $requiredStoriesRepository)
    {
        $this->requiredStoriesRepository = $requiredStoriesRepository;
    }

    /**
     * @throws Exception
     */
    public function handle(CreateRequiredStoryCommand $command): void
    {
        $requiredStory = new RequiredStory(
            $command->getId(),
            $command->getStoryId(),
            $command->getStudentId(),
            new DateTimeImmutable('now', new DateTimeZone('Europe/Moscow')),
            $command->getCreatedBy(),
            $command->getStartDate(),
            $command->getDays(),
            $command->getStatus(),
            $command->getMetadata(),
        );
        $this->requiredStoriesRepository->create($requiredStory);
    }
}

<?php

declare(strict_types=1);

namespace modules\edu\RequiredStory\Edit;

use DomainException;
use Exception;
use modules\edu\RequiredStory\repo\RequiredStoriesRepository;

class EditRequiredStoryHandler
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
    public function handle(EditRequiredStoryCommand $command): void
    {
        $requiredStory = $this->requiredStoriesRepository->findById($command->getId());
        if ($requiredStory === null) {
            throw new DomainException('Required story not found');
        }
        $requiredStory->update(
            $command->getStoryId(),
            $command->getStudentId(),
            $command->getStartDate(),
            $command->getDays(),
            $command->getStatus(),
            $command->getMetadata(),
        );
        $this->requiredStoriesRepository->update($requiredStory);
    }
}

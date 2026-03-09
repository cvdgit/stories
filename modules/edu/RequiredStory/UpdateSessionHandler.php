<?php

declare(strict_types=1);

namespace modules\edu\RequiredStory;

use Exception;
use modules\edu\RequiredStory\repo\RequiredStoriesRepository;
use modules\edu\RequiredStory\repo\RequiredStorySessionRepository;
use modules\edu\StoryContent\StoryContentService;

class UpdateSessionHandler
{
    /**
     * @var RequiredStoriesRepository
     */
    private $requiredStoriesRepository;
    /**
     * @var RequiredStorySessionRepository
     */
    private $requiredStorySessionRepository;
    /**
     * @var StoryContentService
     */
    private $storyContentService;

    public function __construct(
        RequiredStoriesRepository $requiredStoriesRepository,
        RequiredStorySessionRepository $requiredStorySessionRepository,
        StoryContentService $storyContentService
    ) {
        $this->requiredStoriesRepository = $requiredStoriesRepository;
        $this->requiredStorySessionRepository = $requiredStorySessionRepository;
        $this->storyContentService = $storyContentService;
    }

    /**
     * @throws Exception
     */
    public function handle(UpdateSessionCommand $command): void
    {
        $requiredStory = $this->requiredStoriesRepository->findRequiredStory(
            $command->getStoryId(),
            $command->getStudentId(),
        );
        if ($requiredStory === null) {
            return;
        }
        $session = $this->requiredStorySessionRepository->find(
            $requiredStory->getId(),
            $command->getDate(),
        );
        if ($session === null) {
            return;
        }

        $collection = $this->storyContentService->getStudentFactContentItemsDetail(
            $command->getStudentId(),
            $requiredStory->getStoryId(),
        );

        $session->setFact(
            $this->storyContentService->getStudentFactContentItemsCountByDate(
                $collection,
                $command->getStudentId(),
                $command->getStoryId(),
                $command->getDate(),
            ),
        );
        $this->requiredStorySessionRepository->update($session);

        /*$requiredStory->calcStatus(
            $this->storyContentService->getStoryTotalContentItems($command->getStoryId()),
            $this->storyContentService->getStudentFactContentItemsCount(
                $command->getStoryId(),
                $command->getStudentId(),
            )
        );
        $this->requiredStoriesRepository->updateStatus(
            $requiredStory->getId(),
            $requiredStory->getStatus()
        );*/
    }
}

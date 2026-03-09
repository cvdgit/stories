<?php

declare(strict_types=1);

namespace modules\edu\RequiredStory\Delete;

use modules\edu\RequiredStory\repo\RequiredStoriesRepository;
use yii\db\Exception;

class DeleteRequiredStoryHandler
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
    public function handle(DeleteRequiredStoryCommand $command): void
    {
        $this->requiredStoriesRepository->delete($command->getId());
    }
}

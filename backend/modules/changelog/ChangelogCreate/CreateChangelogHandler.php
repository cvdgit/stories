<?php

declare(strict_types=1);

namespace backend\modules\changelog\ChangelogCreate;

use backend\modules\changelog\models\Changelog;
use common\services\TransactionManager;
use common\Tags\TagsService;

class CreateChangelogHandler
{
    private $transactionManager;
    private $tagsService;

    public function __construct(TransactionManager $transactionManager, TagsService $tagsService)
    {
        $this->transactionManager = $transactionManager;
        $this->tagsService = $tagsService;
    }

    public function handle(CreateChangelogForm $command): void
    {
        $this->transactionManager->wrap(function() use ($command) {
            $changelog = Changelog::create($command->title, $command->text, strtotime($command->created));
            $changelog->updateTags($this->tagsService->processTags($command->tags));
            if (!$changelog->save()) {
                throw new \DomainException('Changelog save exception');
            }
        });
    }
}

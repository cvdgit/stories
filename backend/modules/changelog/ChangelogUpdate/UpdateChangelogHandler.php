<?php

declare(strict_types=1);

namespace backend\modules\changelog\ChangelogUpdate;

use backend\modules\changelog\models\Changelog;
use common\services\TransactionManager;
use common\Tags\TagsService;
use yii\web\NotFoundHttpException;

class UpdateChangelogHandler
{
    private $transactionManager;
    private $tagsService;

    public function __construct(TransactionManager $transactionManager, TagsService $tagsService)
    {
        $this->transactionManager = $transactionManager;
        $this->tagsService = $tagsService;
    }

    /**
     * @throws NotFoundHttpException
     */
    public function handle(UpdateChangelogCommand $command): void
    {
        $changelog = Changelog::findOne($command->getId());
        if ($changelog === null) {
            throw new NotFoundHttpException('Запись не найдена');
        }
        $this->transactionManager->wrap(function() use ($changelog, $command) {
            $changelog->updateChangelog($command->getTitle(), $command->getText(), $command->getStatus(), time());
            $changelog->updateTags($this->tagsService->processTags($command->getTags()));
            if (!$changelog->save()) {
                throw new \DomainException('Changelog update exception');
            }
        });
    }
}

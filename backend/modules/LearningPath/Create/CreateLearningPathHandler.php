<?php

declare(strict_types=1);

namespace backend\modules\LearningPath\Create;

use backend\modules\LearningPath\models\LearningPath;
use DomainException;

class CreateLearningPathHandler
{
    public function handle(CreateLearningPathCommand $command): void
    {
        $learningPath = LearningPath::create(
            $command->getUuid(),
            $command->getName(),
            $command->getPayload(),
            $command->getUserId()
        );
        if (!$learningPath->save()) {
            throw new DomainException('Ошибка при создании карты знаний');
        }
    }
}

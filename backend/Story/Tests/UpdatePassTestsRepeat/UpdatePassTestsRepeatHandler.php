<?php

declare(strict_types=1);

namespace backend\Story\Tests\UpdatePassTestsRepeat;

use common\models\StoryTestQuestion;
use DomainException;

class UpdatePassTestsRepeatHandler
{
    public function handle(UpdatePassTestsRepeatForm $command): void
    {
        $questionModel = StoryTestQuestion::findOne($command->questionId);
        if ($questionModel === null) {
            throw new DomainException("Вопрос не найден");
        }
        $questionModel->max_prev_items = $command->repeat;
        if (!$questionModel->save()) {
            throw new DomainException("Ошибка при сохранении вопроса");
        }
    }
}

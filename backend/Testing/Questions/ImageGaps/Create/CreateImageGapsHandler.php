<?php

declare(strict_types=1);

namespace backend\Testing\Questions\ImageGaps\Create;

use backend\models\question\QuestionType;
use common\models\StoryTestQuestion;

class CreateImageGapsHandler
{
    public function handle(CreateQuestionCommand $command): int
    {
        $questionModel = StoryTestQuestion::create(
            $command->getTestId(),
            $command->getName(),
            QuestionType::IMAGE_GAPS,
            null,
            0,
            $command->getImage()
        );
        if (!$questionModel->save()) {
            throw new \DomainException('Ошибка при создании вопроса');
        }
        return $questionModel->id;
    }
}

<?php

declare(strict_types=1);

namespace backend\Testing\Questions\Gpt\Update;

use common\components\ModelDomainException;
use common\models\StoryTestQuestion;
use DomainException;
use Exception;

class UpdateGptQuestionHandler
{
    /**
     * @throws Exception
     */
    public function handle(UpdateGptQuestionCommand $command): void
    {
        $question = StoryTestQuestion::findOne($command->getQuestionId());
        if ($question === null) {
            throw new DomainException('Вопрос не найден');
        }
        $question->name = $command->getName();
        $question->regions = $command->getPayload();
        if (!$question->save()) {
            throw ModelDomainException::create($question);
        }
    }
}

<?php

declare(strict_types=1);

namespace backend\Testing\Questions\Gpt\Create;

use backend\models\question\QuestionType;
use common\components\ModelDomainException;
use common\models\StoryTestQuestion;
use Exception;

class CreateGptQuestionHandler
{
    /**
     * @throws Exception
     */
    public function handle(CreateGptQuestionCommand $command): void
    {
        $question = StoryTestQuestion::create($command->getTestId(), $command->getName(), QuestionType::GPT_QUESTION);
        $question->regions = $command->getPayload();
        $question->weight = 1;
        if (!$question->save()) {
            throw ModelDomainException::create($question);
        }
    }
}

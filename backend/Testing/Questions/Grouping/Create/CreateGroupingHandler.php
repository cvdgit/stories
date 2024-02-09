<?php

declare(strict_types=1);

namespace backend\Testing\Questions\Grouping\Create;

use backend\models\question\QuestionType;
use common\components\ModelDomainException;
use common\models\StoryTestAnswer;
use common\models\StoryTestQuestion;
use common\services\TransactionManager;
use Exception;
use yii\helpers\Json;

class CreateGroupingHandler
{
    /**
     * @var TransactionManager
     */
    private $transactionManager;

    public function __construct(TransactionManager $transactionManager)
    {
        $this->transactionManager = $transactionManager;
    }

    /**
     * @throws Exception
     */
    public function handle(CreateGroupingCommand $command): void
    {
        $this->transactionManager->wrap(function() use ($command) {
            $json = Json::decode($command->getPayload());
            $questionId = $this->createQuestion($command->getTestId(), $command->getName(), $command->getPayload());
            $this->createAnswers($questionId, $json);
        });
    }

    private function createQuestion(int $quizId, string $name, string $payload): int
    {
        $questionModel = StoryTestQuestion::create($quizId, $name, QuestionType::GROUPING);
        $questionModel->regions = $payload;
        $questionModel->weight = 1;
        if (!$questionModel->save()) {
            throw ModelDomainException::create($questionModel);
        }
        return $questionModel->id;
    }

    public function createAnswers(int $questionId, array $payload): void
    {
        $groups = $payload["groups"];
        foreach ($groups as $group) {
            $answerModel = StoryTestAnswer::create($questionId, $group["title"], StoryTestAnswer::CORRECT_ANSWER);
            $answerModel->description = Json::encode($group);
            if (!$answerModel->save()) {
                throw ModelDomainException::create($answerModel);
            }
        }
    }
}

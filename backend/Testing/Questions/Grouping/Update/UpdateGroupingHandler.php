<?php

declare(strict_types=1);

namespace backend\Testing\Questions\Grouping\Update;

use backend\models\question\QuestionType;
use common\components\ModelDomainException;
use common\models\StoryTestAnswer;
use common\models\StoryTestQuestion;
use common\services\TransactionManager;
use Exception;
use yii\helpers\Json;

class UpdateGroupingHandler
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
    public function handle(UpdateGroupingCommand $command): void
    {
        $questionModel = StoryTestQuestion::findOne($command->getQuestionId());
        if ($questionModel === null) {
            throw new \DomainException("Вопрос не найден");
        }

        $questionModel->name = $command->getName();
        $questionModel->regions = $command->getPayload();

        $this->transactionManager->wrap(function() use ($questionModel, $command) {

            if (!$questionModel->save()) {
                throw ModelDomainException::create($questionModel);
            }

            StoryTestAnswer::deleteAll(['story_question_id' => $questionModel->id]);
            $this->createAnswers($questionModel->id, Json::decode($command->getPayload()));
        });
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

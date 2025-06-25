<?php

declare(strict_types=1);

namespace backend\Testing\Questions\Math\Create;

use backend\models\question\QuestionType;
use common\components\ModelDomainException;
use common\models\StoryTestQuestion;
use common\services\TransactionManager;
use Exception;
use Yii;
use yii\helpers\Json;

class CreateMathQuestionHandler
{
    /**
     * @var TransactionManager
     */
    private $transactionManager;

    public function __construct(TransactionManager $transactionManager) {
        $this->transactionManager = $transactionManager;
    }

    /**
     * @throws Exception
     */
    public function handle(CreateMathQuestionCommand $command): void
    {
        $question = StoryTestQuestion::create($command->getTestId(), $command->getName(), QuestionType::MATH_QUESTION, null, 1);
        $question->regions = Json::encode($command->getPayload()->asArray());
        $question->weight = 1;

        $this->transactionManager->wrap(static function() use ($question, $command): void {

            if (!$question->save()) {
                throw ModelDomainException::create($question);
            }

            $toInsertRows = [];
            foreach ($command->getPayload()->getAnswers() as $insertAnswer) {
                $toInsertRows[] = [
                    'story_question_id' => $question->id,
                    'name' => $insertAnswer['value'],
                    'order' => 1,
                    'is_correct' => $insertAnswer['correct'] ? 1 : 0,
                    'region_id' => $insertAnswer['id'],
                    'description' => $insertAnswer['placeholder'] ?? null
                ];
            }
            $insertCommand = Yii::$app->db->createCommand();
            $insertCommand->batchInsert('story_test_answer', ['story_question_id', 'name', 'order', 'is_correct', 'region_id', 'description'], $toInsertRows);
            $insertCommand->execute();
        });
    }
}

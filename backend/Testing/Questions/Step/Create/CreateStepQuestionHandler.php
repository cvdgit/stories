<?php

declare(strict_types=1);

namespace backend\Testing\Questions\Step\Create;

use backend\models\question\QuestionType;
use common\components\ModelDomainException;
use common\models\StoryTestQuestion;
use common\services\TransactionManager;
use Exception;
use Yii;
use yii\helpers\Json;

class CreateStepQuestionHandler
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
    public function handle(CreateStepQuestionCommand $command): void
    {
        $question = StoryTestQuestion::create($command->getTestId(), $command->getName(), QuestionType::STEP_QUESTION);
        $question->regions = Json::encode([
            'job' => $command->getJob(),
            'steps' => $command->getSteps(),
        ]);
        $question->weight = 1;
        $this->transactionManager->wrap(static function() use ($question, $command): void {
            if (!$question->save()) {
                throw ModelDomainException::create($question);
            }
            $toInsertRows = [];
            foreach ($command->getSteps() as $step) {
                $toInsertRows[] = [
                    'story_question_id' => $question->id,
                    'name' => $step->getStepCorrectValues(),
                    'order' => $step->getIndex(),
                    'is_correct' => true,
                    'region_id' => $step->getId(),
                ];
            }
            $insertCommand = Yii::$app->db->createCommand();
            $insertCommand->batchInsert('story_test_answer', ['story_question_id', 'name', 'order', 'is_correct', 'region_id'], $toInsertRows);
            $insertCommand->execute();
        });
    }
}

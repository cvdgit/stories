<?php

declare(strict_types=1);

namespace backend\Testing\Questions\Column\Create;

use backend\models\question\QuestionType;
use common\components\ModelDomainException;
use common\models\StoryTestQuestion;
use common\services\TransactionManager;
use Exception;
use Yii;
use yii\helpers\Json;

class CreateColumnQuestionHandler
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
    public function handle(CreateColumnQuestionCommand $command): void
    {
        $question = StoryTestQuestion::create($command->getTestId(), $command->getName(), QuestionType::COLUMN_QUESTION);
        $question->regions = Json::encode($command->getPayload());
        $question->weight = 1;
        $this->transactionManager->wrap(static function() use ($question, $command): void {
            if (!$question->save()) {
                throw ModelDomainException::create($question);
            }
            $columns = [
                'story_question_id' => $question->id,
                'name' => $command->getAnswerName(),
                'order' => 1,
                'is_correct' => true,
            ];
            $insertCommand = Yii::$app->db->createCommand();
            $insertCommand->insert('story_test_answer', $columns);
            $insertCommand->execute();
        });
    }
}

<?php

declare(strict_types=1);

namespace backend\Testing\Questions\ImageGaps\Update;

use common\components\ModelDomainException;
use common\models\StoryTestAnswer;
use common\models\StoryTestQuestion;
use common\services\TransactionManager;
use yii\helpers\Json;

class UpdateHandler
{
    /**
     * @var TransactionManager
     */
    private $transactionManager;

    public function __construct(TransactionManager $transactionManager)
    {
        $this->transactionManager = $transactionManager;
    }

    public function handle(UpdateCommand $command): void
    {
        $questionModel = StoryTestQuestion::findOne($command->getQuestionId());
        if ($questionModel === null) {
            throw new \DomainException('Вопрос не найден');
        }
        $questionModel->name = $command->getName();
        $questionModel->regions = $command->getPayload();
        $questionModel->max_prev_items = $command->getMaxPrevItems();
        if (!$questionModel->save()) {
            throw new \DomainException('Question save exception');
        }

        $json = Json::decode($command->getPayload());
        $this->transactionManager->wrap(static function() use ($command, $json) {
            StoryTestAnswer::deleteAll(['story_question_id' => $command->getQuestionId()]);
            foreach ($json['fragments'] as $fragment) {

                foreach ($fragment['items'] as $item) {

                    if (!$item['correct']) {
                        continue;
                    }

                    $answerModel = StoryTestAnswer::create($command->getQuestionId(), $item['title'], StoryTestAnswer::CORRECT_ANSWER);
                    $answerModel->description = Json::encode($item);
                    $answerModel->region_id = $item['id'];
                    if (!$answerModel->save()) {
                        throw ModelDomainException::create($answerModel);
                    }
                }
            }
        });
    }
}

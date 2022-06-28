<?php

namespace backend\services;

use backend\models\pass_test\CreatePassTestForm;
use backend\models\question\QuestionType;
use common\components\ModelDomainException;
use common\models\StoryTestAnswer;
use common\models\StoryTestQuestion;
use common\services\TransactionManager;
use yii\helpers\Json;

class PassTestService
{

    private $transactionManager;

    public function __construct(TransactionManager $transactionManager)
    {
        $this->transactionManager = $transactionManager;
    }

    public function createQuestion(int $quizId, string $name, string $payload): int
    {
        $questionModel = StoryTestQuestion::create($quizId, $name, QuestionType::PASS_TEST);
        $questionModel->regions = $payload;
        if (!$questionModel->save()) {
            throw ModelDomainException::create($questionModel);
        }
        return $questionModel->id;
    }

    public function createAnswers(int $questionId, string $json): void
    {

        $payload = Json::decode($json);
        $fragments = $payload['fragments'];

        foreach ($fragments as $fragment) {

            foreach ($fragment['items'] as $item) {

                if (!$item['correct']) {
                    continue;
                }

                $answerModel = StoryTestAnswer::create($questionId, $item['title'], StoryTestAnswer::CORRECT_ANSWER);
                $answerModel->description = Json::encode($item);
                if (!$answerModel->save()) {
                    throw ModelDomainException::create($answerModel);
                }
            }
        }
    }

    public function create(int $quizId, CreatePassTestForm $form): void
    {
        if (!$form->validate()) {
            throw ModelDomainException::create($form);
        }

        $this->transactionManager->wrap(function() use ($quizId, $form) {

            $questionId = $this->createQuestion($quizId, $form->name, $form->payload);
            $this->createAnswers($questionId, $form->payload);
        });
    }

    public function update(StoryTestQuestion $questionModel, CreatePassTestForm $form): void
    {
        if (!$form->validate()) {
            throw ModelDomainException::create($form);
        }

        $questionModel->name = $form->name;
        $questionModel->regions = $form->payload;

        $this->transactionManager->wrap(function() use ($questionModel, $form) {

            if (!$questionModel->save()) {
                throw ModelDomainException::create($questionModel);
            }

            StoryTestAnswer::deleteAll(['story_question_id' => $questionModel->id]);
            $this->createAnswers($questionModel->id, $form->payload);
        });
    }
}

<?php

namespace backend\services;

use backend\models\pass_test\PassTestForm;
use backend\models\question\QuestionType;
use backend\Testing\Questions\ImageUpload\ImageUploadCommand;
use backend\Testing\Questions\ImageUpload\ImageUploadHandler;
use common\components\ModelDomainException;
use common\models\StoryTestAnswer;
use common\models\StoryTestQuestion;
use common\services\TransactionManager;
use DomainException;
use Exception;
use Yii;
use yii\helpers\Json;

class PassTestService
{
    private $transactionManager;
    /**
     * @var ImageUploadHandler
     */
    private $imageUploadHandler;

    public function __construct(TransactionManager $transactionManager, ImageUploadHandler $imageUploadHandler)
    {
        $this->transactionManager = $transactionManager;
        $this->imageUploadHandler = $imageUploadHandler;
    }

    public function createQuestion(int $quizId, string $name, string $payload, int $maxPrevItems = 0, int $weight = 1): int
    {
        $questionModel = StoryTestQuestion::create($quizId, $name, QuestionType::PASS_TEST);
        $questionModel->regions = $payload;
        $questionModel->max_prev_items = $maxPrevItems;
        $questionModel->weight = $weight;
        if (!$questionModel->save()) {
            throw ModelDomainException::create($questionModel);
        }
        return $questionModel->id;
    }

    public function createAnswers(int $questionId, array $payload): void
    {
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

    /**
     * @throws Exception
     */
    public function create(int $quizId, PassTestForm $form): void
    {
        if (!$form->validate()) {
            throw ModelDomainException::create($form);
        }

        $questionModel = StoryTestQuestion::create($quizId, $form->name, QuestionType::PASS_TEST);
        $questionModel->regions = $form->payload;
        $questionModel->max_prev_items = $form->max_prev_items;

        $json = Json::decode($form->payload);
        $questionModel->weight = $this->calcWeight($json);

        $this->transactionManager->wrap(function() use ($questionModel, $json, $form) {

            if ($form->imageFile !== null) {
                $fileName = Yii::$app->security->generateRandomString() . '.' . $form->imageFile->extension;
                $this->imageUploadHandler->handle(new ImageUploadCommand(
                    $questionModel->getImagesPath(),
                    $fileName,
                    $form->imageFile,
                    $questionModel->image
                ));
                $questionModel->image = "thumb_" . $fileName;
            }

            if (!$questionModel->save()) {
                throw ModelDomainException::create($questionModel);
            }

            $this->createAnswers($questionModel->id, $json);
        });
    }

    public function update(int $questionId, PassTestForm $form): void
    {
        if (!$form->validate()) {
            throw ModelDomainException::create($form);
        }

        $questionModel = StoryTestQuestion::findOne($questionId);
        if ($questionModel === null) {
            throw new DomainException("Question with id $questionId not found");
        }

        $questionModel->name = $form->name;
        $questionModel->regions = $form->payload;
        $questionModel->sort_view = $form->view;
        $questionModel->max_prev_items = $form->max_prev_items;

        $json = Json::decode($form->payload);
        $questionModel->weight = $this->calcWeight($json);

        $this->transactionManager->wrap(function() use ($questionModel, $json, $form) {

            if ($form->imageFile !== null) {
                $fileName = Yii::$app->security->generateRandomString() . '.' . $form->imageFile->extension;
                $this->imageUploadHandler->handle(new ImageUploadCommand(
                    $questionModel->getImagesPath(),
                    $fileName,
                    $form->imageFile,
                    $questionModel->image
                ));
                $questionModel->image = "thumb_" . $fileName;
            }

            if (!$questionModel->save()) {
                throw ModelDomainException::create($questionModel);
            }

            StoryTestAnswer::deleteAll(['story_question_id' => $questionModel->id]);
            $this->createAnswers($questionModel->id, $json);
        });
    }

    public function calcWeight(array $json): int
    {
        $weight = count($json['fragments']);
        if ($weight === 0) {
            $weight = 1;
        }
        return $weight;
    }
}

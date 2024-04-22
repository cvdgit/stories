<?php

declare(strict_types=1);

namespace backend\services;

use backend\models\drag_words\CreateDragWordsForm;
use backend\models\drag_words\UpdateDragWordsForm;
use backend\models\question\QuestionType;
use common\components\ModelDomainException;
use common\models\StoryTestAnswer;
use common\models\StoryTestQuestion;
use common\services\TransactionManager;
use DomainException;
use Yii;
use yii\base\Exception;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\imagine\Image;
use yii\web\UploadedFile;

class DragWordsService
{
    private $transactionManager;

    public function __construct(TransactionManager $transactionManager)
    {
        $this->transactionManager = $transactionManager;
    }

    public function createAnswers(int $questionId, string $json): void
    {
        $payload = Json::decode($json);
        $fragments = $payload['fragments'];
        foreach ($fragments as $fragment) {
            if (!$fragment['correct']) {
                continue;
            }
            $answerModel = StoryTestAnswer::create($questionId, $fragment['title'], StoryTestAnswer::CORRECT_ANSWER);
            $answerModel->description = Json::encode($fragment);
            if (!$answerModel->save()) {
                throw ModelDomainException::create($answerModel);
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function create(int $quizId, CreateDragWordsForm $form): void
    {
        if (!$form->validate()) {
            throw ModelDomainException::create($form);
        }

        $questionModel = StoryTestQuestion::create($quizId, $form->name, QuestionType::DRAG_WORDS);
        $questionModel->regions = $form->payload;

        $this->transactionManager->wrap(function() use ($questionModel, $form) {

            if ($form->imageFile !== null) {
                $fileName = Yii::$app->security->generateRandomString() . '.' . $form->imageFile->extension;
                $this->uploadQuestionImage(
                    $questionModel->getImagesPath(),
                    $fileName,
                    $form->imageFile,
                    $questionModel->image
                );
                $questionModel->image = "thumb_" . $fileName;
            }

            if (!$questionModel->save()) {
                throw ModelDomainException::create($questionModel);
            }

            $this->createAnswers($questionModel->id, $form->payload);
        });
    }

    /**
     * @throws \Exception
     */
    public function update(int $questionId, UpdateDragWordsForm $form): void
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

        $this->transactionManager->wrap(function () use ($questionModel, $form): void {

            if ($form->imageFile !== null) {
                $fileName = Yii::$app->security->generateRandomString() . '.' . $form->imageFile->extension;
                $this->uploadQuestionImage(
                    $questionModel->getImagesPath(),
                    $fileName,
                    $form->imageFile,
                    $questionModel->image
                );
                $questionModel->image = "thumb_" . $fileName;
            }

            if (!$questionModel->save()) {
                throw ModelDomainException::create($questionModel);
            }
            StoryTestAnswer::deleteAll(['story_question_id' => $questionModel->id]);
            $this->createAnswers($questionModel->id, $form->payload);
        });
    }

    /**
     * @throws Exception
     */
    private function uploadQuestionImage(string $folder, string $fileName, UploadedFile $uploadedFile, string $oldImageFileName = null): void
    {
        FileHelper::createDirectory($folder);

        $imagePath = $folder . $fileName;
        $uploadedFile->saveAs($imagePath);

        $thumbImagePath = $folder . 'thumb_' . $fileName;
        Image::resize($imagePath, 330, 500)->save($thumbImagePath, ['quality' => 100]);

        if (!empty($oldImageFileName)) {
            $oldImages = [
                $folder . $oldImageFileName,
                $folder . 'thumb_' . $oldImageFileName,
            ];
            foreach ($oldImages as $oldImagePath) {
                if (file_exists($oldImagePath)) {
                    FileHelper::unlink($oldImagePath);
                }
            }
        }
    }
}

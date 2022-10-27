<?php

namespace backend\services;

use backend\forms\TestingAnswerForm;
use backend\models\answer\DefaultAnswerModel;
use backend\models\answer\SequenceAnswerModel;
use common\models\StoryTestAnswer;
use DomainException;
use http\Exception\RuntimeException;
use Imagine\Image\ManipulatorInterface;
use Yii;
use yii\db\Query;
use yii\helpers\FileHelper;
use yii\imagine\Image;
use yii\web\UploadedFile;

class AnswerService
{
    public function createAnswer(DefaultAnswerModel $form): StoryTestAnswer
    {
        if (!$form->validate()) {
            throw new DomainException('DefaultAnswerModel not valid');
        }
        return StoryTestAnswer::createFromRelation(
            $form->name,
            $form->correct,
            $form->description
        );
    }

    public function createSequenceAnswer(SequenceAnswerModel $form): StoryTestAnswer
    {
        if (!$form->validate()) {
            throw new DomainException('DefaultAnswerModel not valid');
        }
        return StoryTestAnswer::createSequenceFromRelation(
            $form->name,
            $form->order
        );
    }

    public function create(int $questionId, TestingAnswerForm $form, string $folder, UploadedFile $image = null): void
    {
        $fileName = null;
        if ($image !== null) {

            $fileName = Yii::$app->security->generateRandomString() . '.' . $image->extension;
            $this->uploadImage($image, $folder, $fileName);

            $prefix = 'thumb_';
            $this->createImageThumb($folder, $fileName, $prefix);

            $fileName = $prefix . $fileName;
        }

        $answer = StoryTestAnswer::create($questionId, $form->name, $form->is_correct, null, $fileName);
        if (!$answer->save()) {
            throw new DomainException('Answer save exception');
        }
    }

    public function update(StoryTestAnswer $answer, TestingAnswerForm $form, string $folder, UploadedFile $image = null): void
    {
        if ($image !== null) {

            $prefix = 'thumb_';

            if ($answer->haveImage()) {
                $this->deleteImages($folder, $answer->image, $prefix);
            }

            $fileName = Yii::$app->security->generateRandomString() . '.' . $image->extension;
            $this->uploadImage($image, $folder, $fileName);

            $this->createImageThumb($folder, $fileName, $prefix);

            $fileName = $prefix . $fileName;
            $answer->updateAnswerImage($fileName);
        }

        $answer->updateAnswer($form->name, $form->is_correct);
        if (!$answer->save()) {
            throw new DomainException('Answer save exception');
        }
    }

    private function uploadImage(UploadedFile $image, string $folder, string $fileName): void
    {
        $path = $folder . '/' . $fileName;
        $image->saveAs($path);
    }

    private function createImageThumb(string $folder, string $fileName, string $prefix): void
    {
        $image = $folder . '/' . $fileName;
        if (!file_exists($image)) {
            throw new RuntimeException('Answer image file not found');
        }
        $path = $folder . '/' . $prefix . $fileName;
        Image::thumbnail($image, 110, 100, ManipulatorInterface::THUMBNAIL_INSET)
            ->save($path, ['jpeg_quality' => 100]);
    }

    private function deleteImages(string $folder, string $fileName, string $prefix): void
    {
        $images = [
            $fileName,
            str_replace($prefix, '', $fileName),
        ];
        foreach ($images as $imageFileName) {
            $path = $folder . '/' . $imageFileName;
            if (file_exists($path)) {
                FileHelper::unlink($path);
            }
        }
    }

    public function delete(string $folder, int $answerId): void
    {
        $answerImage = (new Query())
            ->select('image')
            ->from('story_test_answer')
            ->where(['id' => $answerId])
            ->scalar();

        Yii::$app->db->createCommand()
            ->delete('story_test_answer', ['id' => $answerId])
            ->execute();

        if ($answerImage !== null) {
            $this->deleteImages($folder, $answerImage, 'thumb_');
        }
    }

    public function deleteImage(int $answerId, string $folder): void
    {
        $answerImage = (new Query())
            ->select('image')
            ->from('story_test_answer')
            ->where(['id' => $answerId])
            ->scalar();
        if ($answerImage !== null) {
            $this->deleteImages($folder, $answerImage, 'thumb_');
        }
        Yii::$app->db->createCommand()
            ->update('story_test_answer', ['image' => null], ['id' => $answerId])
            ->execute();
    }
}

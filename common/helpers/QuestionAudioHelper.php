<?php

namespace common\helpers;

use common\models\StoryTestQuestion;
use yii\helpers\FileHelper;

class QuestionAudioHelper
{

    public static function deleteAudioFile(string $filePath): void
    {
        if (file_exists($filePath)) {
            FileHelper::unlink($filePath);
        }
    }

    public static function setQuestionAudioFile(int $questionId, string $fileName = null): ?string
    {
        if (($questionModel = StoryTestQuestion::findOne($questionId)) === null) {
            throw new \DomainException('Question not found');
        }
        $questionModel->updateAudioFile($fileName);
        if (!$questionModel->save()) {
            throw new \DomainException('setQuestionAudioFile exception');
        }
        return $questionModel->getAudioFileUrl();
    }
}
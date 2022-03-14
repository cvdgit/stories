<?php

namespace backend\models;

use common\helpers\QuestionAudioHelper;
use common\models\StoryTestQuestion;
use yii\base\Model;

class DeleteQuestionAudioModel extends Model
{

    public $question_id;

    public function rules(): array
    {
        return [
            ['question_id', 'required'],
            ['question_id', 'integer'],
            ['question_id', 'exist', 'targetClass' => StoryTestQuestion::class, 'targetAttribute' => ['question_id' => 'id']],
        ];
    }

    public function deleteAudioFile(): void
    {
        if (!$this->validate()) {
            throw new \DomainException('DeleteQuestionAudioModel not valid');
        }

        $questionModel = StoryTestQuestion::findOne($this->question_id);
        if ($questionModel->haveAudioFile()) {
            QuestionAudioHelper::deleteAudioFile($questionModel->getAudioFilePath());
            QuestionAudioHelper::setQuestionAudioFile($questionModel->id);
        }
    }
}
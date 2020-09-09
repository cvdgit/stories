<?php

namespace frontend\models;

use common\models\UserStudent;
use common\models\StudentQuestionProgress AS Progress;
use yii\base\Model;
use yii\web\BadRequestHttpException;

class StudentQuestionProgress extends Model
{

    public $student_id;
    public $question_id;
    public $progress;
    public $test_id;

    public function rules()
    {
        return [
            [['student_id', 'question_id', 'test_id'], 'required'],
            [['student_id', 'question_id', 'progress', 'test_id'], 'integer'],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserStudent::class, 'targetAttribute' => ['student_id' => 'id']],
        ];
    }

    public function updateProgress()
    {
        if (!$this->validate()) {
            throw new BadRequestHttpException('Progress model not valid');
        }
        $model = Progress::findProgressModel($this->student_id, $this->test_id);
        if ($model !== null) {
            $model->updateProgress($this->progress);
        }
        else {
            $model = Progress::create($this->student_id, $this->question_id, $this->progress, $this->test_id);
        }
        $model->save();
    }

}
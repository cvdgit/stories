<?php

namespace frontend\models;

use common\models\StoryTest;
use common\models\StoryTestRun;
use common\models\UserStudent;
use yii\base\Model;

class CreateStoryTestRun extends Model
{

    public $test_id;
    public $student_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['test_id', 'student_id'], 'required'],
            [['test_id', 'student_id'], 'integer'],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserStudent::class, 'targetAttribute' => ['student_id' => 'id']],
            [['test_id'], 'exist', 'skipOnError' => true, 'targetClass' => StoryTest::class, 'targetAttribute' => ['test_id' => 'id']],
        ];
    }

    public function createStoryTestRun()
    {
        if (!$this->validate()) {
            throw new \DomainException('Model not valid');
        }
        $model = StoryTestRun::create(
            $this->test_id,
            $this->student_id
        );
        return $model->save();
    }

}
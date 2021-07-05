<?php

namespace backend\models\editor;

use common\models\StorySlide;
use common\models\StoryStoryTest;

class QuestionForm extends BaseForm
{

    public $test_id;
    public $required;
    public $content;

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [['test_id', 'required'], 'integer'],
        ]);
    }

    public function attributeLabels(): array
    {
        return array_merge(parent::attributeLabels(), [
            'test_id' => 'Тест',
            'required' => 'Тест обязателен для прохождения',
        ]);
    }

    public function afterCreate(StorySlide $slideModel): void
    {
        $model = StoryStoryTest::create($slideModel->story_id, $this->test_id);
        if (!$model->validate()) {
            throw new \DomainException('Model not valid');
        }
        $model->save(false);

        $slideModel->setQuestionSlide();
    }
}

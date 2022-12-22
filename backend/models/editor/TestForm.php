<?php

namespace backend\models\editor;

use common\models\StorySlide;
use common\models\StoryStoryTest;
use common\models\StoryTest;
use DomainException;

class TestForm extends TextForm
{
    public $test_id;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['test_id'], 'required'],
            [['test_id'], 'integer'],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'text' => 'Заголовок',
            'test_id' => 'Тест',
        ]);
    }

    public function afterCreate(StorySlide $slideModel): void
    {
        parent::afterCreate($slideModel);
        $model = StoryStoryTest::create($slideModel->story_id, $this->test_id);
        if (!$model->validate()) {
            throw new DomainException(implode('<br>', $model->getErrorSummary(true)));
        }
        $model->save(false);
    }

    public function haveTest(): bool
    {
        return !empty($this->test_id);
    }

    public function getTestName(): string
    {
        if (!$this->haveTest()) {
            return '';
        }
        $test = StoryTest::findOne($this->test_id);
        if ($test === null) {
            return '';
        }
        return $test->title;
    }
}

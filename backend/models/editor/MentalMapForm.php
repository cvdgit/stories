<?php

declare(strict_types=1);

namespace backend\models\editor;

use common\models\StorySlide;
use common\models\StoryStoryTest;
use common\models\StoryTest;
use DomainException;

class MentalMapForm extends BaseForm
{
    public $mental_map_id;

    /*public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['update'] = ['story_id', 'slide_id', 'test_id', 'block_id', 'required', 'lesson_id'];
        return $scenarios;
    }*/

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [['story_id', 'mental_map_id'], 'required'],
            //[['story_id', 'test_id', 'required', 'lesson_id'], 'integer'],
            //[['story_id', 'test_id'], 'unique', 'targetAttribute' => ['story_id', 'test_id'], 'targetClass' => StoryStoryTest::class, 'message' => 'Невозможно добавить т.к. этот тест в эту историю уже добавлен', 'on' => 'default'],
        ]);
    }

    public function attributeLabels(): array
    {
        return array_merge(parent::attributeLabels(), [
            'mental_map_id' => 'Ментальная карта',
            //'required' => 'Тест обязателен для прохождения',
        ]);
    }

    /*public function afterCreate(StorySlide $slideModel): void
    {
        parent::afterCreate($slideModel);
        $model = StoryStoryTest::create($slideModel->story_id, $this->test_id);
        if (!$model->validate()) {
            throw new \DomainException('Model not valid');
        }
        $model->save(false);
        $slideModel->setQuestionSlide();
    }*/

    /*public function haveTest(): bool
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
    }*/
}

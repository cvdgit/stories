<?php

declare(strict_types=1);

namespace backend\models\editor;

class MentalMapForm extends BaseForm
{
    public $mental_map_id;
    public $required;
    public $use_slide_image;
    public $name;
    public $texts;
    public $image;
    public $tree_view;

    public function init(): void
    {
        parent::init();
        $this->name = 'Ментальная карта';
        $this->use_slide_image = true;
        $this->tree_view = false;
    }

    /*public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['update'] = ['story_id', 'slide_id', 'test_id', 'block_id', 'required', 'lesson_id'];
        return $scenarios;
    }*/

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [['story_id', 'name'], 'required'],
            [['story_id', 'required'], 'integer'],
            [['use_slide_image', 'tree_view'], 'boolean'],
            ['texts', 'safe'],
            [['image', 'name'], 'string'],
            ['mental_map_id', 'string', 'max' => 36],
            //[['story_id', 'test_id'], 'unique', 'targetAttribute' => ['story_id', 'test_id'], 'targetClass' => StoryStoryTest::class, 'message' => 'Невозможно добавить т.к. этот тест в эту историю уже добавлен', 'on' => 'default'],
        ]);
    }

    public function attributeLabels(): array
    {
        return array_merge(parent::attributeLabels(), [
            'mental_map_id' => 'Ментальная карта',
            'required' => 'Тест обязателен для прохождения',
            'use_slide_image' => 'Использовать изображение со слайда в качестве фона для ментальной карты',
            'name' => 'Название',
            'tree_view' => 'Ментальная карта в виде дерева',
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

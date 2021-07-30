<?php

namespace backend\models\question;

use common\models\StorySlide;
use common\models\StoryTestQuestion;
use DomainException;
use yii\data\ActiveDataProvider;

class UpdateQuestion extends QuestionModel
{

    private $model;

    public function __construct(StoryTestQuestion $model, $config = [])
    {
        $this->model = $model;
        $this->loadModelAttributes();
        parent::__construct($config);
    }

    private function loadModelAttributes()
    {
        foreach ($this->getAttributes() as $name => $value) {
            $modelAttributes = $this->model->getAttributes();
            if (isset($modelAttributes[$name])) {
                $this->{$name} = $this->model->{$name};
            }
        }
    }

    public function getModelID()
    {
        return $this->model->id;
    }

    public function getAnswersDataProvider()
    {
        return new ActiveDataProvider([
            'query' => $this->model->getStoryTestAnswers(),
        ]);
    }

    public function update()
    {
        if (!$this->validate()) {
            throw new DomainException('Model not valid');
        }
        $modelAttributes = $this->model->getAttributes();
        foreach ($this->getAttributes() as $name => $value) {
            if (array_key_exists($name, $modelAttributes)) {
                $this->model->{$name} = $value;
            }
        }
        $this->uploadImage($this->model);
        $this->model->save();
    }

    public function getImageUrl()
    {
        return $this->model->getImageUrl();
    }

    public function haveImage(): bool
    {
        return !empty($this->model->image);
    }

    public function getStorySlides(): array
    {
        return $this->model->getStorySlidesForList();
    }
}
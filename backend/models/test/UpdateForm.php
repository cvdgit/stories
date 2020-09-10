<?php

namespace backend\models\test;

use common\models\StoryTest;
use DomainException;
use yii\base\Model;

class UpdateForm extends Model
{

    public $title;
    public $header;
    public $description_text;
    public $question_params;
    public $incorrect_answer_text;

    private $model;

    public function __construct(StoryTest $model, $config = [])
    {
        $this->model = $model;
        $this->loadModelAttributes();
        parent::__construct($config);
    }

    private function loadModelAttributes()
    {
        foreach ($this->getAttributes() as $name => $value) {
            $this->{$name} = $this->model->{$name};
        }
    }

    public function rules()
    {
        return [
            [['title', 'header', 'question_params'], 'required'],
            [['title', 'header', 'question_params', 'incorrect_answer_text'], 'string', 'max' => 255],
            [['description_text'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => 'Название теста',
            'description_text' => 'Описание',
            'header' => 'Заголовок',
            'question_params' => 'Параметры вопроса',
            'incorrect_answer_text' => 'Текст неправильного ответа',
        ];
    }

    public function updateTestVariant()
    {
        if (!$this->validate()) {
            throw new DomainException('Model not valid');
        }

        foreach ($this->getAttributes() as $name => $value) {
            $this->model->{$name} = $this->{$name};
        }
        $this->model->save();
    }

}
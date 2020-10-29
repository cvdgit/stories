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

    public $taxonName;
    public $taxonValue;

    private $model;

    public function __construct(StoryTest $model, $config = [])
    {
        $this->model = $model;
        $this->loadModelAttributes();
        $this->loadParamAttributes();
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

    private function loadParamAttributes()
    {
        foreach (explode(';', $this->question_params) as $param) {
            [$paramName, $paramValue] = explode('=', $param);
            if (property_exists($this, $paramName)) {
                $this->{$paramName} = $paramValue;
            }
        }
    }

    public function rules()
    {
        return [
            [['title', 'header', 'question_params'], 'required'],
            [['title', 'header', 'question_params', 'incorrect_answer_text'], 'string', 'max' => 255],
            [['taxonName', 'taxonValue'], 'string', 'max' => 255],
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
            'taxonName' => 'Таксон',
            'taxonValue' => 'Значение таксона',
        ];
    }

    public function updateTestVariant()
    {
        if (!$this->validate()) {
            throw new DomainException('Model not valid');
        }

        foreach ($this->getAttributes() as $name => $value) {
            $modelAttributes = $this->model->getAttributes();
            if (isset($modelAttributes[$name])) {
                $this->model->{$name} = $this->{$name};
            }
        }
        $this->model->question_params = sprintf('taxonName=%1s;taxonValue=%2s', $this->taxonName, $this->taxonValue);

        $this->model->save();
    }

}
<?php

namespace backend\models\test;

use yii\base\Model;

class BaseVariantModel extends Model
{

    public $title;
    public $header;
    public $description_text;
    public $question_params;
    public $incorrect_answer_text;
    public $wrong_answers_params;

    public $taxonName;
    public $taxonValue;

    public $wrongAnswerTaxonNames = [];
    public $wrongAnswerTaxonValues = [];

    public function rules()
    {
        return [
            [['title', 'header'], 'required'],
            [['title', 'header', 'question_params', 'incorrect_answer_text'], 'string', 'max' => 255],
            [['taxonName', 'taxonValue'], 'string', 'max' => 255],
            [['description_text'], 'string'],
            [['wrongAnswerTaxonNames', 'wrongAnswerTaxonValues'], 'safe'],
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
            'wrongAnswerTaxonNames' => 'Таксон',
            'wrongAnswerTaxonValues' => 'Значение',
        ];
    }

    protected function createWrongAnswersParams()
    {
        $taxonItems = [];
        for ($i = 0, $iMax = count($this->wrongAnswerTaxonNames); $i < $iMax; $i++) {
            $taxonItems[] = sprintf('taxonName=%1s;taxonValue=%2s', $this->wrongAnswerTaxonNames[$i], $this->wrongAnswerTaxonValues[$i]);
        }
        return implode('|', $taxonItems);
    }

}
<?php

declare(strict_types=1);

namespace backend\Testing\Questions\Math;

use yii\base\Model;

abstract class MathQuestionForm extends Model
{
    public $name;
    public $job;
    public $answers;
    public $inputAnswer;
    public $inputAnswerValue;
    public $inputAnswerId;
    public $fragments;

    public function rules(): array
    {
        return [
            [['name'], 'required'],
            ['name', 'string', 'max' => 1024],
            [['job'], 'string'],
            [['answers', 'fragments'], 'safe'],
            ['inputAnswer', 'boolean'],
            ['inputAnswerValue', 'string', 'max' => 512],
            ['inputAnswerId', 'string', 'max' => 36],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Вопрос',
            'job' => 'Задание',
            'answers' => 'Варианты ответов',
            'inputAnswer' => 'Ввод ответа пользователем',
            'inputAnswerValue' => 'Значение',
        ];
    }
}

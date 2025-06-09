<?php

declare(strict_types=1);

namespace backend\Testing\Questions\Math;

use yii\base\Model;

abstract class MathQuestionForm extends Model
{
    public $name;
    public $job;
    public $answers;
    public $haveJob;
    public $haveAnswers;

    public function rules(): array
    {
        return [
            [['name', 'haveJob', 'haveAnswers'], 'required'],
            ['name', 'string', 'max' => 1024],
            [['job'], 'string'],
            ['answers', 'safe'],
            [['haveJob', 'haveAnswers'], 'string'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Вопрос',
            'job' => 'Задание',
            'answers' => 'Варианты ответов',
            'haveJob' => 'Задание',
            'haveAnswers' => 'Варианты ответов',
        ];
    }
}

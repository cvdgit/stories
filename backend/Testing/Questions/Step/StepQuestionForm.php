<?php

declare(strict_types=1);

namespace backend\Testing\Questions\Step;

use yii\base\Model;

abstract class StepQuestionForm extends Model
{
    public $name;
    public $job;
    public $steps;

    public function rules(): array
    {
        return [
            [['name', 'job'], 'required'],
            ['name', 'string', 'max' => 1024],
            [['job', 'steps'], 'safe'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Вопрос',
            'job' => 'Задание',
            'steps' => 'Этапы',
        ];
    }
}

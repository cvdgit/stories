<?php

declare(strict_types=1);

namespace backend\Testing\Questions\Gpt;

use yii\base\Model;

abstract class GptQuestionForm extends Model
{
    public $name;
    public $job;
    public $promptId;

    public function rules(): array
    {
        return [
            [['name', 'job', 'promptId'], 'required'],
            ['name', 'string', 'max' => 1024],
            ['job', 'string'],
            ['promptId', 'string', 'max' => 36],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Вопрос',
            'job' => 'Текст задания',
            'promptId' => 'Промт для проверки ответа',
        ];
    }
}

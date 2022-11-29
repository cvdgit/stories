<?php

declare(strict_types=1);

namespace backend\forms;

use yii\base\Model;

class WordListPoetryForm extends Model
{
    public $name;
    public $line_per_question;

    public function init(): void
    {
        parent::init();
        $this->line_per_question = 7;
    }

    public function rules(): array
    {
        return [
            [['name', 'line_per_question'], 'required'],
            ['name', 'string'],
            ['line_per_question', 'integer', 'min' => 1],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Название',
            'line_per_question' => 'Строк в вопросе',
        ];
    }
}

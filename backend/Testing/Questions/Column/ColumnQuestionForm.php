<?php

declare(strict_types=1);

namespace backend\Testing\Questions\Column;

use yii\base\Model;

abstract class ColumnQuestionForm extends Model
{
    public $name;
    public $firstDigit;
    public $secondDigit;
    public $sign;
    public $result;
    public $payload;
    public $isCorrectAnswerDelay;

    public function rules(): array
    {
        return [
            [['name', 'firstDigit', 'secondDigit', 'sign', 'result'], 'required'],
            ['name', 'string', 'max' => 1024],
            [['firstDigit', 'secondDigit', 'result'], 'integer', 'min' => 0],
            [['firstDigit', 'secondDigit'], 'string', 'min' => 1],
            ['sign', 'string', 'max' => 1],
            ['isCorrectAnswerDelay', 'boolean'],
            ['payload', 'safe'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Вопрос',
            'sign' => 'Знак',
            'firstDigit' => '#1',
            'secondDigit' => '#2',
            'result' => 'Ответ',
            'isCorrectAnswerDelay' => 'Задержка (40 сек.) перед показом правильного ответа после неправильного',
        ];
    }

    public function getIsShowCorrectDelay(): bool
    {
        return $this->isCorrectAnswerDelay === '1';
    }
}

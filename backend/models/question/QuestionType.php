<?php

namespace backend\models\question;

class QuestionType
{

    public const ONE = 0;
    public const MANY = 1;
    public const REGION = 2;

    public static function asArray()
    {
        return [
            self::ONE => 'Один ответ',
            self::MANY => 'Множественный выбор',
            self::REGION => 'Выбор области',
        ];
    }

}
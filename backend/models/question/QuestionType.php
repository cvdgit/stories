<?php

namespace backend\models\question;

use DomainException;

class QuestionType
{

    public const ONE = 0;
    public const MANY = 1;
    public const REGION = 2;
    public const SEQUENCE = 3;

    private $type;

    public function __construct(int $type)
    {
        $types = self::asArray();
        if (!isset($types[$type])) {
            throw new DomainException('Unknown type');
        }
        $this->type = $type;
    }

    public static function asArray(): array
    {
        return [
            self::ONE => 'Один ответ',
            self::MANY => 'Множественный выбор',
            self::REGION => 'Выбор области',
            self::SEQUENCE => 'Последовательность',
        ];
    }

    public function isRegion(): bool
    {
        return $this->type === self::REGION;
    }

    public function isSequence(): bool
    {
        return $this->type === self::SEQUENCE;
    }
}

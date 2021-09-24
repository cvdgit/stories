<?php

namespace backend\components\import;

class WordListModifierBuilder
{

    public static function build(int $type, $words): WordListModifierInterface
    {
        $className = '';
        switch ($type) {
            case 0:
                $className = DefaultWordListModifier::class;
                break;
            case 1:
                $className = ReverseWordListModifier::class;
                break;
            case 2:
                $className = FirstColumnWordListModifier::class;
                break;
            case 3:
                $className = SecondColumnWordListModifier::class;
                break;
        }
        if (empty($className)) {
            throw new \DomainException('Unknown type');
        }
        return new $className($words);
    }
}

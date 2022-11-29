<?php

declare(strict_types=1);

namespace backend\components\import\poetry;

class LineBreaker
{
    /**
     * @return array<string|false>
     */
    public function wordSafeBreak(string $str): array
    {
        for ($a = 0, $c = strlen($str); $a < $c && !($a >= $c / 2 && $str[$a] === ' '); $a++);
        /*for($middle = floor(strlen($str)/2); $middle >= 0 && $str[$middle] !== ' '; $middle--);
        if ($middle < 0)
            return array('', $str);*/
        return [substr($str, 0, $a), substr($str, $a + 1)];
    }
}

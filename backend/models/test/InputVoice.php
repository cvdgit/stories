<?php

namespace backend\models\test;

class InputVoice
{

    public static function asArray(): array
    {
        return [
            'Google русский' => 'Google русский (ru-RU)',
            'Microsoft Irina - Russian (Russia)' => 'Microsoft Irina Desktop - Russian (ru-RU)',
            'Google US English' => 'Google US English (en-US)',
            'Google UK English Female' => 'Google UK English Female',
            'Google UK English Male' => 'Google UK English Male',
            'Microsoft David - English (United States)' => 'Microsoft David Desktop - English (United States) (en-US)',
            'Microsoft Zira - English (United States)' => 'Microsoft Zira Desktop - English (United States) (en-US)',
        ];
    }

}

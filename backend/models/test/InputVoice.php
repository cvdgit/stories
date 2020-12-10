<?php

namespace backend\models\test;

class InputVoice
{

    public static function asArray(): array
    {
        return [
            'Google русский' => 'Google русский (ru-RU)',
            'Microsoft Irina Desktop - Russian' => 'Microsoft Irina Desktop - Russian (ru-RU)',
            'Google US English' => 'Google US English (en-US)',
            'Microsoft David Desktop - English (United States)' => 'Microsoft David Desktop - English (United States) (en-US)',
            'Microsoft Zira Desktop - English (United States)' => 'Microsoft Zira Desktop - English (United States) (en-US)',
        ];
    }

}
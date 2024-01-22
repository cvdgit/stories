<?php

declare(strict_types=1);

namespace frontend\GptChat;

use yii\base\Model;

class GptChatForm extends Model
{
    public $text;

    public function rules(): array
    {
        return [
            ["text", "required"],
            ["text", "string", "max" => 80],
        ];
    }
}

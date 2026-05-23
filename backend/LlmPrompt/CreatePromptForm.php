<?php

declare(strict_types=1);

namespace backend\LlmPrompt;

use yii\base\Model;

class CreatePromptForm extends Model
{
    public $name;
    public $key;
    public $prompt;

    public function rules(): array
    {
        return [
            [['name', 'key', 'prompt'], 'required'],
            [['name', 'key'], 'string', 'max' => 255],
            ['prompt', 'string'],
        ];
    }
}

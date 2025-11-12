<?php

declare(strict_types=1);

namespace backend\modules\gpt\Prompts;

use yii\db\ActiveRecord;

/**
 * @property string $id
 * @property string $name
 * @property string $prompt
 * @property int $created_at
 * @property string $key
 */
class LlmPrompt extends ActiveRecord
{
    public static function findByKey(string $key): ?self
    {
        /** @var LlmPrompt|null $model */
        $model = self::findOne(['key' => $key]);
        return $model;
    }
}

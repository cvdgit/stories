<?php

declare(strict_types=1);

namespace frontend\Retelling;

use yii\db\ActiveRecord;

/**
 * @property string $id
 * @property int $slide_id
 * @property string $name
 * @property string $questions
 * @property int $with_questions
 * @property int $created_by
 * @property int $created_at
 * @property int $updated_at
 * @property array $payload
 */
class Retelling extends ActiveRecord
{
    public function updateRetelling(int $withQuestions, string $questions, int $threshold): void
    {
        $this->with_questions = $withQuestions;
        $this->questions = $questions;
        $this->updated_at = time();
    }

    public function getRetellingPayload(): array
    {
        return $this->payload ?? [];
    }

    public function getRetellingSettingsPayload(): array
    {
        return $this->getRetellingPayload()['settings'] ?? [];
    }

    public function getSettingsThreshold(): ?int
    {
        $threshold = $this->getRetellingSettingsPayload()['threshold'];
        if ($threshold !== null) {
            return (int) $threshold;
        }
        return null;
    }

    public static function findBySlideId(int $slideId): ?self
    {
        return self::findOne(['slide_id' => $slideId]);
    }
}

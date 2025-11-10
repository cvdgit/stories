<?php

declare(strict_types=1);

namespace backend\AiStoryAssist;

use yii\db\ActiveRecord;

/**
 * @property string $id
 * @property string $title
 * @property string|null $text
 * @property array|null $payload
 * @property int $user_id
 * @property int $created_at
 * @property int $updated_at
 * @property int|null $story_id
 */
class StoryThread extends ActiveRecord
{
    public static function create(string $id, string $title, int $userId, string $text = null, array $messages = []): self
    {
        $model = new self();
        $model->id = $id;
        $model->title = $title;
        $model->user_id = $userId;
        $model->created_at = time();
        $model->updated_at = time();
        $model->text = $text;
        $model->payload = $messages;
        return $model;
    }

    /**
     * @param int $userId
     * @return array<StoryThread>
     */
    public static function findAllByUser(int $userId): array
    {
        return self::find()
            ->where(['user_id' => $userId])
            ->orderBy(['updated_at' => SORT_DESC])
            ->all();
    }

    public static function findByUser(string $id, int $userId): ?self
    {
        /** @var StoryThread|null $model */
        $model = self::find()
            ->where(['id' => $id, 'user_id' => $userId])
            ->orderBy(['updated_at' => SORT_DESC])
            ->one();
        return $model;
    }

    public function updateThread(array $payload): void
    {
        $this->payload = $payload;
        $this->updated_at = time();
    }

    public function setStory(int $storyId, string $title): void
    {
        $this->story_id = $storyId;
        $this->title = $title;
        $this->updated_at = time();
    }
}

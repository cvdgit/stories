<?php

namespace api\modules\v1\models;

use yii\db\ActiveRecord;

class StoryTest extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return 'story_test';
    }

    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        return [
            'id',
            'title',
        ];
    }

    /**
     * @inheritdoc
     */
    public function extraFields(): array
    {
        return [];
    }
}
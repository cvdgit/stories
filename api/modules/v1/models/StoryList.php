<?php

namespace api\modules\v1\models;

use common\models\Category;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

class StoryList extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%story_list}}';
    }

    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        return [
            'id',
            'name',
        ];
    }

    public function getCategories(): ActiveQuery
    {
        return $this->hasMany(Category::class, ['id' => 'category_id'])
            ->viaTable('story_list_category', ['story_list_id' => 'id']);
    }

    public function getCategoryIds(): array
    {
        return array_map(static function($category) {
            return $category->id;
        }, $this->categories);
    }
}
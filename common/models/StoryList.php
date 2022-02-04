<?php

namespace common\models;

use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "story_list".
 *
 * @property int $id
 * @property string $name
 * @property int $created_at
 * @property int $updated_at
 *
 * @property StoryListCategory[] $storyListCategories
 * @property Category[] $categories
 */
class StoryList extends ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
            'saveRelations' => [
                'class' => SaveRelationsBehavior::class,
                'relations' => ['categories'],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'story_list';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getStoryListCategories(): ActiveQuery
    {
        return $this->hasMany(StoryListCategory::class, ['story_list_id' => 'id']);
    }

    public function getCategories(): ActiveQuery
    {
        return $this->hasMany(Category::class, ['id' => 'category_id'])
            ->viaTable('story_list_category', ['story_list_id' => 'id']);
    }

    public static function create(string $name, array $categories): self
    {
        $model = new self();
        $model->name = $name;
        $model->categories = $categories;
        return $model;
    }
}

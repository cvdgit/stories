<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "story_list_category".
 *
 * @property int $story_list_id
 * @property int $category_id
 *
 * @property Category $category
 * @property StoryList $storyList
 */
class StoryListCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'story_list_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['story_list_id', 'category_id'], 'required'],
            [['story_list_id', 'category_id'], 'integer'],
            [['story_list_id', 'category_id'], 'unique', 'targetAttribute' => ['story_list_id', 'category_id']],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],
            [['story_list_id'], 'exist', 'skipOnError' => true, 'targetClass' => StoryList::className(), 'targetAttribute' => ['story_list_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'story_list_id' => 'Story List ID',
            'category_id' => 'Category ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStoryList()
    {
        return $this->hasOne(StoryList::className(), ['id' => 'story_list_id']);
    }
}

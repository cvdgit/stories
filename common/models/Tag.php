<?php

namespace common\models;

use DomainException;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\db\Query;

/**
 * This is the model class for table "tag".
 *
 * @property int $id
 * @property int $frequency
 * @property string $name
 *
 * @property StoryTag[] $storyTags
 * @property Story[] $stories
 */
class Tag extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tag';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['frequency'], 'integer'],
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'frequency' => 'Frequency',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStoryTags()
    {
        return $this->hasMany(StoryTag::className(), ['tag_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStories()
    {
        return $this->hasMany(Story::className(), ['id' => 'story_id'])->viaTable('story_tag', ['tag_id' => 'id']);
    }

    /**
     * @param string $name
     * @return Tag[]
     */
    public static function findAllByName($name)
    {
        return Tag::find()->where(['like', 'name', $name])->limit(50)->all();
    }

    public static function findModelByName($name): self
    {
        if (($model = self::findOne(['name' => $name])) !== null) {
            return $model;
        }
        throw new DomainException('Тэг не найден');
    }

    public static function getPopularTags()
    {
        return (new Query())
            ->select([
                'name',
                new Expression("CONCAT('/stories/tag/', `name`) AS url"),
                'frequency AS weight'
            ])
            ->from(self::tableName())
            ->where('frequency > 0')
            ->indexBy('name')
            ->all();
    }

}

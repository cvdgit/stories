<?php

namespace modules\edu\models;

use common\models\Story;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "edu_lesson".
 *
 * @property int $id
 * @property int $topic_id
 * @property string $name
 * @property int $order
 *
 * @property EduLessonStory[] $eduLessonStories
 * @property Story[] $stories
 * @property EduTopic $topic
 */
class EduLesson extends ActiveRecord
{

    public function behaviors(): array
    {
        return [
            'saveRelations' => [
                'class' => SaveRelationsBehavior::class,
                'relations' => [
                    'stories',
                ],
            ],
        ];
    }

    public function transactions(): array
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'edu_lesson';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['topic_id', 'name'], 'required'],
            [['topic_id', 'order'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['topic_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduTopic::class, 'targetAttribute' => ['topic_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'topic_id' => 'Тема',
            'name' => 'Название',
            'order' => 'Order',
        ];
    }

    public function getTopic(): ActiveQuery
    {
        return $this->hasOne(EduTopic::class, ['id' => 'topic_id']);
    }

    public function getTopicArray(): array
    {
        return [$this->topic->id => $this->topic->name];
    }

    public function getEduLessonStories(): ActiveQuery
    {
        return $this->hasMany(EduLessonStory::class, ['lesson_id' => 'id']);
    }

    public function getStories(): ActiveQuery
    {
        return $this->hasMany(Story::class, ['id' => 'story_id'])
            ->viaTable('edu_lesson_story', ['lesson_id' => 'id']);
    }

    public function addStory(int $storyId): void
    {
        $this->stories = array_unique(array_merge(
            array_map(static function($story) {
                return $story->id;
            }, $this->stories),
            [$storyId]));
    }
}

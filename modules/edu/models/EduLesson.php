<?php

namespace modules\edu\models;

use common\models\Story;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;

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
            ->viaTable('edu_lesson_story', ['lesson_id' => 'id'])
            ->innerJoin('edu_lesson_story', 'edu_lesson_story.story_id = story.id')
            ->andWhere(['edu_lesson_story.lesson_id' => $this->id])
            ->orderBy(['edu_lesson_story.order' => SORT_ASC]);
    }

    public function addStory(EduLessonStory $lessonStory): void
    {
        $stories = $this->stories;
        $stories[] = $lessonStory;
        $this->stories = $stories;
    }

    public function getStoriesCount(): int
    {
        return $this->getEduLessonStories()->count();
    }

    private function getLessonStories(): Query
    {
        return (new Query())
            ->from(['lesson' => 'edu_lesson'])
            ->innerJoin(['lesson_story' => 'edu_lesson_story'], 'lesson_story.lesson_id = lesson.id')
            ->where(['lesson.id' => $this->id]);
    }

    public function getLessonStoriesCount(): int
    {
        return $this->getLessonStories()
            ->count('lesson_story.story_id');
    }

    public function getStudentFinishedStoriesCount(int $studentId): int
    {
        $rows = $this->getLessonStories()
            ->select(['story_id' => 'lesson_story.story_id'])
            ->all();

        $storyIds = array_map(static function($row) {
            return $row['story_id'];
        }, $rows);

        return (new Query())
            ->from('story_student_progress')
            ->where(['student_id' => $studentId])
            ->andWhere(['in', 'story_id', $storyIds])
            ->andWhere('progress = 100')
            ->count();
    }

    public function fetchStudentInProgressStoriesCount(int $studentId): int
    {
        $rows = $this->getLessonStories()
            ->select(['story_id' => 'lesson_story.story_id'])
            ->all();

        $storyIds = array_map(static function($row) {
            return $row['story_id'];
        }, $rows);

        return (new Query())
            ->from('story_student_progress')
            ->where(['student_id' => $studentId])
            ->andWhere(['in', 'story_id', $storyIds])
            ->andWhere('progress > 0 AND progress < 100')
            ->count();
    }
}

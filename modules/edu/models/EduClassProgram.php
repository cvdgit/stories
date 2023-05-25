<?php

namespace modules\edu\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "edu_class_program".
 *
 * @property int $id
 * @property int $class_id
 * @property int $program_id
 *
 * @property EduClass $class
 * @property EduProgram $program
 * @property EduTopic[] $eduTopics
 */
class EduClassProgram extends ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'edu_class_program';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['class_id', 'program_id'], 'required'],
            [['class_id', 'program_id'], 'integer'],
            [['class_id', 'program_id'], 'unique', 'targetAttribute' => ['class_id', 'program_id']],
            [['class_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduClass::class, 'targetAttribute' => ['class_id' => 'id']],
            [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduProgram::class, 'targetAttribute' => ['program_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'class_id' => 'Класс',
            'program_id' => 'Предмет',
        ];
    }

    public function getClass(): ActiveQuery
    {
        return $this->hasOne(EduClass::class, ['id' => 'class_id']);
    }

    public function getProgram(): ActiveQuery
    {
        return $this->hasOne(EduProgram::class, ['id' => 'program_id']);
    }

    public function getClassArray(): array
    {
        return ArrayHelper::map(EduClass::find()->orderBy(['name' => SORT_ASC])->asArray()->all(), 'id', 'name');
    }

    public function getProgramArray(): array
    {
        return ArrayHelper::map(EduProgram::find()->orderBy(['name' => SORT_ASC])->asArray()->all(), 'id', 'name');
    }

    public function getEduPath(): string
    {
        return $this->class->name . ' - ' . $this->program->name;
    }

    public function getEduTopics(): ActiveQuery
    {
        return $this->hasMany(EduTopic::class, ['class_program_id' => 'id'])
            ->orderBy(['edu_topic.order' => SORT_ASC]);
    }

    public static function findClassProgram(int $classId, int $programId): ?self
    {
        return self::find()
            ->where(['class_id' => $classId, 'program_id' => $programId])
            ->one();
    }

    public function getEduTopicsWithAccess(int $classBookId): ActiveQuery
    {
        return $this->hasMany(EduTopic::class, ['class_program_id' => 'id'])
            ->innerJoin(['acc' => 'edu_class_book_topic_access'], 'acc.topic_id = edu_topic.id')
            ->andWhere([
                'acc.class_book_id' => $classBookId,
                'acc.class_program_id' => $this->id,
            ]);
    }

    public function createTopicRoute(int $classBookId = null): ?array
    {
        $route = ['/edu/student/topic'];

        if ($classBookId === null) {
            $topics = $this->eduTopics;
        } else {
            $topics = $this->getEduTopics()
                ->innerJoin(['acc' => 'edu_class_book_topic_access'], 'acc.topic_id = edu_topic.id')
                ->andWhere([
                    'acc.class_book_id' => $classBookId,
                    'acc.class_program_id' => $this->id,
                ])
                ->all();
        }

        if (empty($topics)) {
            return null;
        }

        $route['id'] = $topics[0]->id;

        return $route;
    }

    public function getTopicsCount(): int
    {
        return $this->getEduTopics()->count();
    }

    private function getClassProgramStories(): Query
    {
        return (new Query())
            ->from(['topic' => 'edu_topic'])
            ->innerJoin(['lesson' => 'edu_lesson'], 'topic.id = lesson.topic_id')
            ->innerJoin(['lesson_story' => 'edu_lesson_story'], 'lesson_story.lesson_id = lesson.id')
            ->where(['topic.class_program_id' => $this->id]);
    }

    public function getClassProgramStoriesCount(): int
    {
        return $this->getClassProgramStories()
            ->count('DISTINCT lesson_story.story_id');
    }

    public function getStudentFinishedStoriesCount(int $studentId): int
    {
        $rows = $this->getClassProgramStories()
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

    public function getStudentProgress(int $total, int $finished): int
    {
        if ($total === 0) {
            return 0;
        }
        if ($finished === 0) {
            return 0;
        }
        return $finished * 100 / $total;
    }
}

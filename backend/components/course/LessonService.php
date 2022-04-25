<?php

namespace backend\components\course;

use common\models\Lesson;
use common\models\LessonBlock;
use common\models\LessonBlockQuiz;
use common\models\StorySlide;
use common\services\TransactionManager;
use Ramsey\Uuid\Uuid;
use Yii;
use yii\db\Query;

class LessonService
{

    private $transactionManager;

    public function __construct(TransactionManager $transactionManager)
    {
        $this->transactionManager = $transactionManager;
    }

    /**
     * @throws \Exception
     */
    public function saveLessons(int $storyId, array $lessonsJson): void
    {

        $this->transactionManager->wrap(static function() use ($storyId, $lessonsJson) {

            $db = Yii::$app->db;

            $db->createCommand()
                ->delete('lesson', 'story_id = :story', [':story' => $storyId])
                ->execute();

            $blockRows = [];
            $quizRows = [];
            foreach ($lessonsJson as $lesson) {

                $lessonRow = [
                    'type' => $lesson['type'],
                    'uuid' => $lesson['uuid'],
                    'story_id' => $storyId,
                    'name' => $lesson['name'],
                    'order' => $lesson['order'],
                ];
                $db->createCommand()
                    ->insert('lesson', $lessonRow)
                    ->execute();
                $lessonId = $db->getLastInsertID();

                $type = (int)$lesson['type'];

                foreach ($lesson['blocks'] as $block) {

                    $blockRow = [
                        'lesson_id' => $lessonId,
                        'slide_id' => $block['id'],
                        'order' => $block['order'],
                    ];

                    if ($type === LessonType::QUIZ) {
                        $blockRow['quiz_id'] = $block['quiz_id'];
                        $quizRows[] = $blockRow;
                    }
                    else {
                        $blockRows[] = $blockRow;
                    }
                }
            }

            if (count($blockRows) > 0) {
                $db->createCommand()
                    ->batchInsert('lesson_block', ['lesson_id', 'slide_id', 'order'], $blockRows)
                    ->execute();
            }

            if (count($quizRows) > 0) {
                $db->createCommand()
                    ->batchInsert('lesson_block_quiz', ['lesson_id', 'slide_id', 'order', 'quiz_id'], $quizRows)
                    ->execute();
            }
        });
    }

    public function findQuizSlideId(Lesson $lesson): int
    {

        $currentOrder = $lesson->order;
        if ($currentOrder === 1) {
            return -1;
        }

        $blocksQuery = (new Query())
            ->select([
                'lessonId' => 't.id',
                'blockCount' => 'COUNT(t2.lesson_id)',
                'lessonOrder' => 't.order',
            ])
            ->from(['t' => Lesson::tableName()])
            ->leftJoin(['t2' => LessonBlock::tableName()], 't2.lesson_id = t.id')
            ->where(['t.story_id' => $lesson->story_id])
            ->andWhere(['t.type' => LessonType::BLOCKS])
            ->groupBy('t.id');

        $quizQuery = (new Query())
            ->select([
                'lessonId' => 't.id',
                'blockCount' => 'COUNT(t2.lesson_id)',
                'lessonOrder' => 't.order',
            ])
            ->from(['t' => Lesson::tableName()])
            ->leftJoin(['t2' => LessonBlockQuiz::tableName()], 't2.lesson_id = t.id')
            ->where(['t.story_id' => $lesson->story_id])
            ->andWhere(['t.type' => LessonType::QUIZ])
            ->groupBy('t.id');

        $blocksQuery->union($quizQuery, true);

        $lessons = (new Query())
            ->select([
                'id' => 't.lessonId',
                'blocks' => 't.blockCount',
                'ord' => 't.lessonOrder'
            ])
            ->from(['t' => $blocksQuery])
            ->orderBy(['t.lessonOrder' => SORT_ASC])
            ->indexBy('id')
            ->all();

        --$currentOrder;
        $slideIndex = 0;
        foreach ($lessons as $lessonRow) {
            $order = (int)$lessonRow['ord'];
            if ($order <= $currentOrder) {
                $slideIndex += (int)$lessonRow['blocks'];
            }
        }
        return StorySlide::findSlideByNumber($lesson->story_id, $slideIndex)->id;
    }

    public function rebuildLessonsOrder(int $storyId): void
    {
        $lessons = (new Query())
            ->select('*')
            ->from(Lesson::tableName())
            ->where(['story_id' => $storyId])
            ->orderBy(['order' => SORT_ASC])
            ->all();

        $this->transactionManager->wrap(function() use ($lessons) {

            $db = Yii::$app->db;
            $order = 1;
            foreach ($lessons as $lesson) {
                $db->createCommand()
                    ->update(Lesson::tableName(), ['order' => $order], 'id = :id', [':id' => $lesson['id']])
                    ->execute();
                $order++;
            }
        });
    }

    public function deleteLesson(LessonDeleteForm $form): void
    {
        if (!$form->validate()) {
            throw new \DomainException('LessonDeleteForm not valid');
        }
        if (($lessonModel = Lesson::findOne($form->lesson_id)) === null) {
            throw new \DomainException('Lesson not found');
        }
        $lessonModel->delete();

        $this->rebuildLessonsOrder($lessonModel->story_id);
    }

    public function createLesson(LessonCreateForm $form): Lesson
    {
        if (!$form->validate()) {
            throw new \DomainException('LessonCreateForm not valid');
        }

        $lessons = (new Query())
            ->select(['id', '`order`'])
            ->from(Lesson::tableName())
            ->where(['story_id' => $form->course_id])
            ->orderBy(['order' => SORT_ASC])
            ->indexBy('id')
            ->all();

        $targetOrder = $form->lesson_order;

        if ($form->insert_position === 'before') {

        }

        if ($form->insert_position === 'after') {
            $targetOrder++;
        }

        $db = Yii::$app->db;
        $order = 1;
        foreach ($lessons as $lesson) {

            if ($order < $targetOrder) {
                $order++;
                continue;
            }

            $lessonId = $lesson['id'];
            $db->createCommand()
                ->update(Lesson::tableName(), ['order' => $order + 1], 'id = :id', [':id' => $lessonId])
                ->execute();

            $order++;
        }

        $lessonModel = Lesson::create(Uuid::uuid4(), $form->course_id, 'Новый урок', LessonType::BLOCKS, $targetOrder);
        if (!$lessonModel->save()) {
            throw new \DomainException('Create Lesson exception');
        }

        return $lessonModel;
    }

    public function updateLessonsOrder(array $lessons): void
    {
        $this->transactionManager->wrap(function() use ($lessons) {

            $db = Yii::$app->db;
            foreach ($lessons as $lesson) {
                /** @var LessonOrderForm $lesson */
                $db->createCommand()
                    ->update(Lesson::tableName(), ['order' => $lesson->lesson_order], 'id = :id', [':id' => $lesson->lesson_id])
                    ->execute();
            }
        });
    }

    public function updateLessonName(LessonNameForm $form): void
    {
        if (!$form->validate()) {
            throw new \DomainException('LessonNameForm not valid');
        }
        if (($lessonModel = Lesson::findOne($form->lesson_id)) === null) {
            throw new \DomainException('Lesson not found');
        }
        $lessonModel->updateName($form->lesson_name);
    }
}

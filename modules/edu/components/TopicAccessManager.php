<?php

declare(strict_types=1);

namespace modules\edu\components;

use yii\db\Expression;
use yii\db\Query;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;

class TopicAccessManager
{
    public function getStudentLessonAccess(int $classProgramId, int $studentId): array
    {
        $query = (new Query())
            ->select([
                'topicId' => 'edu_topic.id',
                'lessonId' => 'edu_lesson.id',
                'progress' => 'ROUND(IFNULL(SUM(story_student_progress.progress) / COUNT(edu_lesson_story.story_id), 0))',
            ])
            ->from('edu_topic')
            ->innerJoin('edu_lesson', 'edu_topic.id = edu_lesson.topic_id')
            ->leftJoin('edu_lesson_story', 'edu_lesson.id = edu_lesson_story.lesson_id')
            ->leftJoin('story_student_progress', [
                'edu_lesson_story.story_id' => new Expression('story_student_progress.story_id'),
                'story_student_progress.student_id' => $studentId,
            ])
            ->where(['edu_topic.class_program_id' => $classProgramId])
            ->groupBy(['topicId', 'lessonId'])
            ->orderBy([
                'edu_topic.order' => SORT_ASC,
                'edu_lesson.order' => SORT_ASC,
            ]);
        $lessonRows = $query->all();

        $lessonAccess = [];
        $accessNext = false;
        foreach ($lessonRows as $lessonRow) {
            $progress = (int)$lessonRow['progress'];
            $access = false;
            if ($progress > 0) {
                $access = true;
            }
            if ($accessNext) {
                $access = true;
                $accessNext = false;
            }
            $lessonAccess[$lessonRow['lessonId']] = ['access' => $access];
            if ($progress === 100) {
                $accessNext = true;
            }
        }

        $haveAccess = count(array_filter($lessonAccess, static function($item) {
            return $item['access'];
        })) > 0;

        if (!$haveAccess) {
            $firstKey = array_key_first($lessonAccess);
            $lessonAccess[$firstKey]['access'] = true;
        }

        $accessRows = (new Query())
            ->select(['lesson_id', 'access_type'])
            ->from('edu_lesson_access')
            ->where(['class_program_id' => $classProgramId])
            ->all();

        $lessonAccessSettings = array_combine(
            array_column($accessRows, 'lesson_id'),
            array_column($accessRows, 'access_type')
        );

        foreach ($lessonRows as $lessonRow) {
            $accessType = $lessonAccessSettings[$lessonRow['lessonId']] ?? null;
            if ($accessType !== null) {
                $lessonAccess[$lessonRow['lessonId']] = ['access' => ($accessType === 'access')];
            }
        }

        return $lessonAccess;
    }

    /**
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function checkAccessLesson(int $classProgramId, int $lessonId, int $studentId): void
    {
        $lessonAccess = $this->getStudentLessonAccess($classProgramId, $studentId);
        if (!isset($lessonAccess[$lessonId])) {
            throw new BadRequestHttpException('Ошибка доступа к уроку');
        }
        if ($lessonAccess[$lessonId]['access'] === false) {
            throw new ForbiddenHttpException('Доступ закрыт преподавателем');
        }
    }
}

<?php

declare(strict_types=1);

namespace modules\edu\query;

use common\models\UserStudent;
use modules\edu\query\GetStoryTests\SlideMentalMap;
use modules\edu\query\GetStoryTests\StoryTestsFetcher;
use yii\base\InvalidConfigException;
use yii\db\Expression;
use yii\db\Query;
use yii\web\NotFoundHttpException;

class StudentStoryDurationFetcher
{
    /**
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function fetch(int $studentId, int $storyId, string $targetDate): string
    {
        $betweenBegin = new Expression("UNIX_TIMESTAMP('$targetDate 00:00:00')");
        $betweenEnd = new Expression("UNIX_TIMESTAMP('$targetDate 23:59:59')");

        /*$timeQuery = (new Query())
            ->select([
                'session_time' => 'MAX(story_student_stat.created_at) - MIN(story_student_stat.created_at)',
            ])
            ->from('story_student_stat')
            ->where(['story_student_stat.story_id' => $storyId, 'story_student_stat.student_id' => $studentId])
            ->andWhere(['between', 'story_student_stat.created_at', $betweenBegin, $betweenEnd])
            ->groupBy('story_student_stat.session');

        $query = (new Query())
            ->select([
                'total_time' => 'SEC_TO_TIME(SUM(t.session_time))',
            ])
            ->from(['t' => $timeQuery]);

        return (string)$query->scalar();*/

        $student = UserStudent::findOne($studentId);
        if ($student === null) {}

        $storyStatQuery = (new Query())
            ->select(['sessionSec' => new Expression('MAX(t.created_at) - MIN(t.created_at)')])
            ->from(['t' => 'story_student_stat'])
            ->where([
                't.story_id' => $storyId,
                't.student_id' => $studentId,
            ])
            ->andWhere(['between', new Expression('t.created_at + (3 * 60 * 60)'), $betweenBegin, $betweenEnd])
        ->groupBy('t.session');

        $slideContent = (new StoryTestsFetcher())->fetch($storyId);
        $storyMentalMaps = $slideContent->find(SlideMentalMap::class);
        $storyMentalMapIds = array_map(static function (SlideMentalMap $m): string {
            return $m->getMentalMapId();
        }, $storyMentalMaps);

        $mentalMapHistoryQuery = (new Query())
            ->select(['sessionSec' => new Expression('15')])
            ->from(['t' => 'mental_map_history'])
            ->where([
                't.story_id' => $storyId,
                't.user_id' => $student->user_id,
            ])
            ->andWhere(['in', 't.mental_map_id', $storyMentalMapIds])
            ->andWhere(['between', new Expression('t.created_at + (3 * 60 * 60)'), $betweenBegin, $betweenEnd]);

        $allQuery = $storyStatQuery->union($mentalMapHistoryQuery);

        $query = (new Query())
            ->select(new Expression('SEC_TO_TIME(SUM(t.sessionSec))'))
            ->from(['t' => $allQuery]);

        return (string)$query->scalar();
    }
}

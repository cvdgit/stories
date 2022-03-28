<?php

namespace frontend\controllers;

use common\models\User;
use common\models\UserStudent;
use frontend\components\learning\form\HistoryFilterForm;
use frontend\components\UserController;
use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\web\NotFoundHttpException;

class TrainingController extends UserController
{

    /**
     * @throws NotFoundHttpException
     */
    public function actionIndex(int $student_id = null): string
    {

        /** @var User $user */
        $user = Yii::$app->user->identity;

        if ($student_id === null) {
            $targetStudent = $user->student();
        }
        else {
            $targetStudent = UserStudent::findOne($student_id);
            $user = $targetStudent->user;
            //if (($targetStudent = $user->findStudentById($student_id)) === null) {
            //    throw new NotFoundHttpException('Студент не найден');
            //}
        }

        $userId = $user->id;
        $studentId = $targetStudent->id;

        $targetDate = date('Y-m-d');
        $filterForm = new HistoryFilterForm();
        if ($this->request->isPost && $filterForm->load($this->request->post()) && $filterForm->validate()) {
            if ($filterForm->action === 'next') {
                $filterForm->setDateNext();
            }
            if ($filterForm->action === 'prev') {
                $filterForm->setDatePrev();
            }
            $targetDate = $filterForm->getFormattedDate();
        }

        $storyBeginDate = new Expression("UNIX_TIMESTAMP('$targetDate 00:00:00')");
        $storyEndDate = new Expression("UNIX_TIMESTAMP('$targetDate 23:59:59')");
        $stories = (new Query())
            ->select([
                'story_id' => 't2.id',
                'story_title' => 't2.title',
            ])
            ->from(['t' => 'user_story_history'])
            ->innerJoin(['t2' => 'story'], 't.story_id = t2.id')
            ->where(['t.user_id' => $userId])
            ->andWhere(['between', 't.updated_at', $storyBeginDate, $storyEndDate])
            ->orderBy(['t.updated_at' => SORT_DESC])
            ->all();

        $betweenBegin = new Expression("UNIX_TIMESTAMP('$targetDate 00:00:00')");
        $betweenEnd = new Expression("UNIX_TIMESTAMP('$targetDate 23:59:59')");

        $hourExpression = new Expression('hour(FROM_UNIXTIME(created_at))');
        $minuteExpression = new Expression('minute(FROM_UNIXTIME(created_at)) DIV 60');

        $minTimeHour = 0;
        $maxTimeHour = 0;

        foreach ($stories as $i => $story) {

            $storyId = $story['story_id'];

            $testRows = (new Query())
                ->select(['test_id' => 't.test_id'])
                ->from(['t' => 'story_story_test'])
                ->where(['t.story_id' => $storyId])
                ->all();
            $testIds = array_map(static function($row) {
                return $row['test_id'];
            }, $testRows);

            $query = (new Query())
                ->select([
                    'question_count' => new Expression('COUNT(id)'),
                    'hour' => $hourExpression,
                    'minute_div' => $minuteExpression,
                ])
                ->from('user_question_history')
                ->where(['student_id' => $studentId])
                ->andWhere(['in', 'test_id', $testIds])
                ->andWhere(['between', 'created_at', $betweenBegin, $betweenEnd])
                ->groupBy([
                    $hourExpression,
                    $minuteExpression
                ]);

            $storyTimes = $query->all();
            $stories[$i]['times'] = $storyTimes;

            if (count($storyTimes) > 0) {
                $minTimeHour = array_reduce($storyTimes, static function($min, $item) {
                    if ($item['hour'] < $min) {
                        return $item['hour'];
                    }
                    return $min;
                }, $storyTimes[0]['hour']);
                $maxTimeHour = array_reduce($storyTimes, static function($max, $item) {
                    if ($item['hour'] > $max) {
                        return $item['hour'];
                    }
                    return $max;
                }, $storyTimes[0]['hour']);
            }
        }

        $interval = 60;
        $times = [
            ['time' => '01:00', 'hour' => 1, 'minute_div' => 0 % $interval],
            ['time' => '02:00', 'hour' => 2, 'minute_div' => 0 % $interval],
            ['time' => '03:00', 'hour' => 3, 'minute_div' => 0 % $interval],
            ['time' => '04:00', 'hour' => 4, 'minute_div' => 0 % $interval],
            ['time' => '05:00', 'hour' => 5, 'minute_div' => 0 % $interval],
            ['time' => '06:00', 'hour' => 6, 'minute_div' => 0 % $interval],
            ['time' => '07:00', 'hour' => 7, 'minute_div' => 0 % $interval],
            ['time' => '08:00', 'hour' => 8, 'minute_div' => 0 % $interval],
            ['time' => '09:00', 'hour' => 9, 'minute_div' => 0 % $interval],
            ['time' => '10:00', 'hour' => 10, 'minute_div' => 0 % $interval],
            ['time' => '11:00', 'hour' => 11, 'minute_div' => 0 % $interval],
            ['time' => '12:00', 'hour' => 12, 'minute_div' => 0 % $interval],
            ['time' => '13:00', 'hour' => 13, 'minute_div' => 0 % $interval],
            ['time' => '14:00', 'hour' => 14, 'minute_div' => 0 % $interval],
            ['time' => '15:00', 'hour' => 15, 'minute_div' => 0 % $interval],
            ['time' => '16:00', 'hour' => 16, 'minute_div' => 0 % $interval],
            ['time' => '17:00', 'hour' => 17, 'minute_div' => 0 % $interval],
            ['time' => '18:00', 'hour' => 18, 'minute_div' => 0 % $interval],
            ['time' => '19:00', 'hour' => 19, 'minute_div' => 0 % $interval],
            ['time' => '20:00', 'hour' => 20, 'minute_div' => 0 % $interval],
            ['time' => '21:00', 'hour' => 21, 'minute_div' => 0 % $interval],
            ['time' => '22:00', 'hour' => 22, 'minute_div' => 0 % $interval],
            ['time' => '23:00', 'hour' => 23, 'minute_div' => 0 % $interval],
        ];

        $columns = [
            ['label' => 'Истории'],
        ];
        foreach ($times as $time) {
            $timeHour = (int) $time['hour'];
            if ($timeHour < $minTimeHour) {
                continue;
            }
            if ($timeHour > $maxTimeHour) {
                break;
            }
            $columns[] = [
                'label' => $time['time'],
            ];
        }

        $models = [];
        foreach ($stories as $story) {

            $model = [
                $story['story_title'],
            ];

            $noTimes = true;
            foreach ($times as $time) {

                $timeHour = (int) $time['hour'];
                if ($timeHour < $minTimeHour) {
                    continue;
                }
                if ($timeHour > $maxTimeHour) {
                    break;
                }

                $value = 0;
                $timeMinuteDiv = (int) $time['minute_div'];
                foreach ($story['times'] as $row) {
                    $questionCount = (int) $row['question_count'];
                    $hour = (int) $row['hour'];
                    $minuteDiv = (int) $row['minute_div'];
                    if ($hour === $timeHour && $minuteDiv === $timeMinuteDiv) {
                        $value = $questionCount;
                        $noTimes = false;
                    }
                }

                $model[] = $value;
            }

            //if (!$noTimes) {
                $models[] = $model;
            //}
        }

        return $this->render('index_new', [
            'students' => $user->students,
            'activeStudentId' => $targetStudent->id,
            'columns' => $columns,
            'models' => $models,
            'filterModel' => $filterForm,
        ]);
    }

}
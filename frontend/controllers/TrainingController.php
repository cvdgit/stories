<?php

namespace frontend\controllers;

use common\helpers\SmartDate;
use common\models\User;
use frontend\components\learning\form\HistoryFilterForm;
use frontend\components\learning\form\WeekFilterForm;
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
    private function getStudent(int $studentId = null)
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;

        if ($studentId === null) {
            $targetStudent = $user->student();
        }
        else {
            if (($targetStudent = $user->findStudentById($studentId)) === null) {
                throw new NotFoundHttpException('Студент не найден');
            }
        }
        return $targetStudent;
    }

    private function getNavItems(string $route, array $students, int $activeStudentId): array
    {
        $items = [];
        foreach ($students as $student) {
            $items[] = [
                'label' => $student->name,
                'url' => [$route, 'student_id' => $student->id],
                'active' => $student->id === $activeStudentId,
            ];
        }
        return $items;
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionIndex(int $student_id = null): string
    {

        $targetStudent = $this->getStudent($student_id);
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
            $filterForm->resetAction();
            $targetDate = $filterForm->getFormattedDate();
        }

        $historyQuery = new Query();

        $hourExpression = new Expression("hour(convert_tz(FROM_UNIXTIME(t.created_at), 'UTC', '+3:00'))");
        $minuteExpression = new Expression("minute(convert_tz(FROM_UNIXTIME(t.created_at), 'UTC', '+3:00')) DIV 60");
        $historyQuery->select([
            'story_id' => 't2.story_id',
            'question_count' => new Expression('COUNT(t.id)'),
            'hour' => $hourExpression,
            'minute_div' => $minuteExpression,
        ]);
        $historyQuery->from(['t' => 'user_question_history']);
        $historyQuery->innerJoin(['t2' => 'story_story_test'], 't.test_id = t2.test_id');
        $historyQuery->where(['t.student_id' => $studentId]);

        $betweenBegin = new Expression("UNIX_TIMESTAMP('$targetDate 00:00:00')");
        $betweenEnd = new Expression("UNIX_TIMESTAMP('$targetDate 23:59:59')");
        $historyQuery->andWhere(['between', 't.created_at', $betweenBegin, $betweenEnd]);

        $historyQuery->groupBy([
            't2.story_id',
            $hourExpression,
            $minuteExpression
        ]);

        $query = new Query();
        $query->select([
            'story_id' => 't.story_id',
            'story_title' => 't2.title',
            'question_count' => 't.question_count',
            'hour' => 't.hour',
            'minute_div' => 't.minute_div',
        ]);
        $query->from(['t' => $historyQuery]);
        $query->innerJoin(['t2' => 'story'], 't.story_id = t2.id');

        $rows = $query->all();
        $stories = [];
        foreach ($rows as $row) {
            $storyId = $row['story_id'];
            if (!isset($stories[$storyId])) {
                $stories[$storyId] = [
                    'story_title' => $row['story_title'],
                    'times' => [],
                ];
            }
            $stories[$storyId]['times'][] = [
                'question_count' => $row['question_count'],
                'hour' => $row['hour'],
                'minute_div' => $row['minute_div'],
            ];
        }

        $minTimeHour = 0;
        $maxTimeHour = 0;
        foreach ($stories as $story) {
            $storyTimes = $story['times'];
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
                    }
                }

                $model[] = $value;
            }

            $models[] = $model;
        }

        return $this->render('index_new', [
            'items' => $this->getNavItems('training/index', Yii::$app->user->identity->students, $targetStudent->id),
            'view' => 'day',
            'viewParams' => [
                'columns' => $columns,
                'models' => $models,
                'filterModel' => $filterForm,
            ],
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionWeek(int $student_id = null): string
    {

        $targetStudent = $this->getStudent($student_id);
        $studentId = $targetStudent->id;

        $filterForm = new WeekFilterForm();
        if ($filterForm->load($this->request->post())) {
            $filterForm->updateWeekDates();
        }

        $rows = $filterForm->search($studentId);

        $stories = [];
        foreach ($rows as $row) {
            $storyId = $row['story_id'];
            if (!isset($stories[$storyId])) {
                $stories[$storyId] = [
                    'story_title' => $row['story_title'],
                    'dates' => [],
                ];
            }
            $stories[$storyId]['dates'][] = [
                'question_count' => $row['question_count'],
                'target_date' => $row['target_date'],
            ];
        }

        $columns = [
            ['label' => 'Истории'],
        ];

        $targetDate = clone $filterForm->getWeekStartDate();
        $dates = [];
        while ($targetDate <= $filterForm->getWeekEndDate()) {
            $currentDate = $targetDate->format('Y-m-d');
            $columns[] = [
                'label' => SmartDate::dateSmart(strtotime($currentDate)),
            ];
            $dates[] = $currentDate;
            $targetDate = $targetDate->modify('+1 day');
        }

        $models = [];
        foreach ($stories as $story) {

            $model = [
                $story['story_title'],
            ];

            foreach ($dates as $currentDate) {

                $value = 0;

                foreach ($story['dates'] as $row) {
                    $questionCount = (int) $row['question_count'];
                    $questionDate = $row['target_date'];
                    if ($questionDate === $currentDate) {
                        $value = $questionCount;
                    }
                }

                $model[] = $value;
            }

            $models[] = $model;
        }

        return $this->render('index_new', [
            'items' => $this->getNavItems('training/week', Yii::$app->user->identity->students, $targetStudent->id),
            'view' => 'week',
            'viewParams' => [
                'filterModel' => $filterForm,
                'columns' => $columns,
                'models' => $models,
            ],
        ]);
    }
}
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

        $filterForm = new HistoryFilterForm();
        if ($this->request->isPost && $filterForm->load($this->request->post()) && $filterForm->validate()) {
            $filterForm->updateDate();
        }

        $rows = $filterForm->search($studentId);

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
                $min = array_reduce($storyTimes, static function($min, $item) {
                    if ($item['hour'] < $min) {
                        return $item['hour'];
                    }
                    return $min;
                }, $storyTimes[0]['hour']);
                if ($minTimeHour === 0) {
                    $minTimeHour = $min;
                }
                if ($min < $minTimeHour) {
                    $minTimeHour = $min;
                }
                $max = array_reduce($storyTimes, static function($max, $item) {
                    if ($item['hour'] > $max) {
                        return $item['hour'];
                    }
                    return $max;
                }, $storyTimes[0]['hour']);
                if ($max > $maxTimeHour) {
                    $maxTimeHour = $max;
                }
            }
        }

        $interval = $filterForm->hours;
        $times = HistoryFilterForm::createTimes($interval);

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
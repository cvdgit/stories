<?php

declare(strict_types=1);

namespace frontend\controllers;

use common\helpers\SmartDate;
use common\models\User;
use common\models\UserStudent;
use frontend\components\learning\form\HistoryFilterForm;
use frontend\components\learning\form\WeekFilterForm;
use frontend\components\UserController;
use frontend\Training\FetchMentalMapHistoryTargetWords\MentalMapHistoryTargetWordsFetcher;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\User as WebUser;

class TrainingController extends UserController
{
    /**
     * @throws NotFoundHttpException
     */
    private function getStudent(User $user, int $studentId = null): UserStudent
    {
        if ($studentId === null) {
            $targetStudent = $user->student();
        } else {
            $targetStudent = $user->findStudentById($studentId);
        }

        if ($targetStudent === null) {
            throw new NotFoundHttpException('Не удалось определить ученика');
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
    public function actionIndex(WebUser $user, Request $request, int $student_id = null): string
    {
        $currentUser = User::findOne($user->getId());
        if ($currentUser === null) {
            throw new NotFoundHttpException('Не удалось определить пользователя');
        }
        $targetStudent = $this->getStudent($currentUser, $student_id);
        $studentId = $targetStudent->id;

        $filterForm = new HistoryFilterForm();
        if ($filterForm->load($request->get()) && $filterForm->validate()) {
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

        $interval = (int) $filterForm->hours;
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
//die(print_r($models));
        $students = array_merge($currentUser->students, $currentUser->parentStudents);

        return $this->render('index_new', [
            'items' => $this->getNavItems('training/index', $students, $targetStudent->id),
            'view' => 'day',
            'viewParams' => [
                'columns' => $columns,
                'models' => $models,
                'filterModel' => $filterForm,
            ],
        ]);
    }

    /**
     * @throws NotFoundHttpException|\DateMalformedStringException
     */
    public function actionWeek(Request $request, WebUser $user, int $student_id = null): string
    {
        $currentUser = User::findOne($user->getId());
        if ($currentUser === null) {
            throw new NotFoundHttpException('Не удалось определить пользователя');
        }
        $targetStudent = $this->getStudent($currentUser, $student_id);
        $studentId = $targetStudent->id;

        $filterForm = new WeekFilterForm();
        if ($filterForm->load($request->get())) {
            $filterForm->updateWeekDates();
        }

        $rows = $filterForm->search($studentId);
        $mentalMapHistoryRows = (new MentalMapHistoryTargetWordsFetcher())->fetch($targetStudent->user_id, $filterForm->getWeekStartDate(), $filterForm->getWeekEndDate());

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

        foreach ($mentalMapHistoryRows as $row) {
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

        $students = array_merge($currentUser->students, $currentUser->parentStudents);

        return $this->render('index_new', [
            'items' => $this->getNavItems('training/week', $students, $targetStudent->id),
            'view' => 'week',
            'viewParams' => [
                'filterModel' => $filterForm,
                'columns' => $columns,
                'models' => $models,
            ],
        ]);
    }
}

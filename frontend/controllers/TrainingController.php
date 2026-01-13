<?php

declare(strict_types=1);

namespace frontend\controllers;

use common\helpers\SmartDate;
use common\models\Story;
use common\models\User;
use common\models\UserStudent;
use DateTimeImmutable;
use DateTimeInterface;
use frontend\components\learning\form\HistoryFilterForm;
use frontend\components\learning\form\WeekFilterForm;
use frontend\components\learning\widget\TableRow;
use frontend\components\UserController;
use frontend\MentalMap\MentalMap;
use frontend\Training\FetchMentalMapHistoryTargetWords\MentalMapHistoryTargetWordsFetcher;
use frontend\Training\MentalMapDayHistoryTargetWordsFetcher;
use frontend\Training\QuizDetailFetcher;
use modules\edu\query\GetStoryTests\SlideMentalMap;
use modules\edu\query\GetStoryTests\SlideTest;
use modules\edu\query\GetStoryTests\StoryTestsFetcher;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\Url;
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

    private function getNavItems(array $students, int $activeStudentId, callable $callback): array
    {
        $items = [];
        foreach ($students as $student) {
            $items[] = [
                'label' => $student->name,
                'url' => $callback($student),
                'active' => $student->id === $activeStudentId,
            ];
        }
        return $items;
    }

    /**
     * @throws NotFoundHttpException
     * @throws InvalidConfigException
     * @throws \Exception
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
        if ($filterForm->load($request->get(), '') && !$filterForm->validate()) {
            $filterForm->initDates();
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

        $targetDate = Yii::$app->formatter->asDate($filterForm->date, 'php:Y-m-d');
        $beginDate = (new DateTimeImmutable($targetDate))->setTime(0, 0);
        $endDate = (new DateTimeImmutable($targetDate))->setTime(23, 59, 59);
        $mentalMapHistoryRows = (new MentalMapDayHistoryTargetWordsFetcher())
            ->fetch($targetStudent->user_id, $beginDate, $endDate, (int) $filterForm->hours);
        foreach ($mentalMapHistoryRows as $row) {
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

        foreach ($stories as $i => $story) {
            if (count($story['times']) === 0 || count($story['times']) === 1) {
                continue;
            }
            $groupTimes = [];
            foreach ($story['times'] as $time) {
                $key = $time['hour'] . '@' . $time['minute_div'];
                if (!isset($groupTimes[$key])) {
                    $groupTimes[$key] = $time;
                    continue;
                }
                $groupTimes[$key]['question_count'] += (int) $time['question_count'];
            }
            $stories[$i]['times'] = array_values($groupTimes);
        }

        $minTimeHour = 0;
        $maxTimeHour = 0;
        foreach ($stories as $story) {
            $storyTimes = $story['times'];
            if (count($storyTimes) > 0) {
                $min = array_reduce($storyTimes, static function ($min, $item) {
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
                $max = array_reduce($storyTimes, static function ($max, $item) {
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

        $storiesProgress = $this->fetchStoriesProgress(
            $targetStudent->id,
            $targetStudent->user_id,
            array_keys($stories),
        );

        $models = [];
        foreach ($stories as $storyId => $story) {
            $info = '';
            if (isset($storiesProgress[$storyId])) {
                $info = $this->storyProgressTooltipText($storiesProgress[$storyId]);
            }

            $model = [
                $story['story_title'] . $info,
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
                        [$hour, $minute] = explode(':', $time['time']);
                        $periodDateFrom = (new DateTimeImmutable($filterForm->date))->setTime((int) $hour, (int) $minute);
                        $periodDateTo = $periodDateFrom->modify("+{$filterForm->hours} minutes");
                        $value = [
                            'count' => $questionCount,
                            'storyId' => $storyId,
                            'testRestarts' => $this->fetchStoryContentRestarts(
                                $targetStudent->id,
                                $storyId,
                                $periodDateFrom,
                                $periodDateTo,
                            ),
                        ];
                    }
                }

                $model[] = $value;
            }

            $models[] = $model;
        }

        $students = array_merge($currentUser->students, $currentUser->parentStudents);

        return $this->render('index_new', [
            'items' => $this->getNavItems($students, $targetStudent->id, static function (UserStudent $student) use ($filterForm): string {
                return Url::to(array_merge(['index', 'student_id' => $student->id], [
                    'date' => $filterForm->date,
                    'hours' => $filterForm->hours,
                ]));
            },),
            'view' => 'day',
            'viewParams' => [
                'studentId' => $studentId,
                'columns' => $columns,
                'models' => $models,
                'filterModel' => $filterForm,
                'storiesProgress' => $storiesProgress,
                'prevUrl' => Url::to(array_merge(
                    ['index', 'student_id' => $studentId],
                    $filterForm->getPrevDate(),
                    ['hours' => $filterForm->hours],
                )),
                'nextUrl' => Url::to(array_merge(
                    ['index', 'student_id' => $studentId],
                    $filterForm->getNextDate(),
                    ['hours' => $filterForm->hours],
                )),
            ],
        ]);
    }

    /**
     * @throws NotFoundHttpException
     * @throws \Exception
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
        if ($filterForm->load($request->get(), '') && !$filterForm->validate()) {
            $filterForm->initDates();
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

        $mentalMapHistoryRows = (new MentalMapHistoryTargetWordsFetcher())->fetch(
            $targetStudent->user_id,
            $filterForm->getWeekStartDate(),
            $filterForm->getWeekEndDate(),
        );
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

        foreach ($stories as $i => $story) {
            if (count($story['dates']) === 0 || count($story['dates']) === 1) {
                continue;
            }
            $groupDates = [];
            foreach ($story['dates'] as $date) {
                $key = $date['target_date'];
                if (!isset($groupDates[$key])) {
                    $groupDates[$key] = $date;
                    continue;
                }
                $groupDates[$key]['question_count'] += (int) $date['question_count'];
            }
            $stories[$i]['dates'] = array_values($groupDates);
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
                'date' => $currentDate,
            ];
            $dates[] = $currentDate;
            $targetDate = $targetDate->modify('+1 day');
        }

        $storiesProgress = $this->fetchStoriesProgress(
            $targetStudent->id,
            $targetStudent->user_id,
            array_keys($stories),
        );

        $models = [];
        foreach ($stories as $storyId => $story) {
            $info = '';
            if (isset($storiesProgress[$storyId])) {
                $info = $this->storyProgressTooltipText($storiesProgress[$storyId]);
            }

            $model = [
                $story['story_title'] . $info,
            ];

            foreach ($dates as $currentDate) {
                $value = 0;
                foreach ($story['dates'] as $row) {
                    $questionCount = (int) $row['question_count'];
                    $questionDate = $row['target_date'];
                    if ($questionDate === $currentDate) {
                        $value = [
                            'count' => $questionCount,
                            'storyId' => $storyId,
                            'testRestarts' => $this->fetchStoryContentRestarts(
                                $targetStudent->id,
                                $storyId,
                                (new DateTimeImmutable($questionDate))->setTime(0, 0),
                                (new DateTimeImmutable($questionDate))->setTime(23, 59, 59),
                            ),
                        ];
                    }
                }
                $model[] = $value;
            }

            $models[] = $model;
        }

        $students = array_merge($currentUser->students, $currentUser->parentStudents);

        return $this->render('index_new', [
            'items' => $this->getNavItems(
                $students,
                $targetStudent->id,
                static function (UserStudent $student) use ($filterForm): string {
                    return Url::to(array_merge(['week', 'student_id' => $student->id], [
                        'year' => $filterForm->year,
                        'week' => $filterForm->week,
                    ]));
                },
            ),
            'view' => 'week',
            'viewParams' => [
                'filterModel' => $filterForm,
                'columns' => $columns,
                'models' => $models,
                'studentId' => $studentId,
                'prevUrl' => Url::to(array_merge(['week', 'student_id' => $studentId], $filterForm->getPrevWeek())),
                'nextUrl' => Url::to(array_merge(['week', 'student_id' => $studentId], $filterForm->getNextWeek())),
            ],
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionDetail(int $story_id, int $student_id, string $date, string $hours, string $time): string
    {
        $story = Story::findOne($story_id);
        if ($story === null) {
            throw new NotFoundHttpException('История не найдена');
        }
        $student = UserStudent::findOne($student_id);
        if ($student === null) {
            throw new NotFoundHttpException('Ученик не найден');
        }

        $beginDate = new \DateTimeImmutable($date . ' ' . $time . ':00');
        $startDate = $beginDate->format('Y-m-d H:i:s');
        $betweenBegin = new Expression("UNIX_TIMESTAMP('$startDate')");

        $endDate = $beginDate->modify('+' . $hours . ' minute');
        $finishDate = $endDate->format('Y-m-d H:i:s');
        $betweenEnd = new Expression("UNIX_TIMESTAMP('$finishDate')");

        $data = (new Query())
            ->select([])
            ->from(['t' => 'mental_map_history'])
            ->where([
                't.story_id' => $story->id,
                't.user_id' => $student->user_id,
            ])
            ->andWhere(['between', new Expression('t.created_at + (3 * 60 * 60)'), $betweenBegin, $betweenEnd])
            ->orderBy(['t.created_at' => SORT_DESC])
            ->all();

        $mentalMapIds = array_map(static function (array $row): string {
            return $row['mental_map_id'];
        }, $data);
        $mentalMapIds = array_unique($mentalMapIds);

        $mentalMaps = [];
        foreach ($mentalMapIds as $mentalMapId) {
            $mentalMap = MentalMap::findOne($mentalMapId);
            if ($mentalMap !== null) {
                $mentalMaps[$mentalMapId] = $mentalMap;
            }
        }

        $quizData = (new QuizDetailFetcher())->fetch(
            $student->id,
            $story->id,
            "UNIX_TIMESTAMP('$startDate')",
            "UNIX_TIMESTAMP('$finishDate')",
        );

        return $this->renderAjax('_detail', [
            'mentalMaps' => $mentalMaps,
            'data' => $data,
            'title' => $story->title . ' - ' . $time . ' (' . $hours . ' мин.)',
            'quizData' => $quizData,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionDetailWeek(int $story_id, int $student_id, string $date): string
    {
        $story = Story::findOne($story_id);
        if ($story === null) {
            throw new NotFoundHttpException('История не найдена');
        }
        $student = UserStudent::findOne($student_id);
        if ($student === null) {
            throw new NotFoundHttpException('Ученик не найден');
        }

        $beginDate = new \DateTimeImmutable($date . ' 00:00:00');
        $startDate = $beginDate->format('Y-m-d H:i:s');
        $betweenBegin = new Expression("UNIX_TIMESTAMP('$startDate')");

        $endDate = new \DateTimeImmutable($date . ' 23:59:59');
        $finishDate = $endDate->format('Y-m-d H:i:s');
        $betweenEnd = new Expression("UNIX_TIMESTAMP('$finishDate')");

        $data = (new Query())
            ->select([])
            ->from(['t' => 'mental_map_history'])
            ->where([
                't.story_id' => $story->id,
                't.user_id' => $student->user_id,
            ])
            ->andWhere(['between', new Expression('t.created_at + (3 * 60 * 60)'), $betweenBegin, $betweenEnd])
            ->orderBy(['t.created_at' => SORT_DESC])
            ->all();

        $mentalMapIds = array_map(static function (array $row): string {
            return $row['mental_map_id'];
        }, $data);
        $mentalMapIds = array_unique($mentalMapIds);

        $mentalMaps = [];
        foreach ($mentalMapIds as $mentalMapId) {
            $mentalMap = MentalMap::findOne($mentalMapId);
            if ($mentalMap !== null) {
                $mentalMaps[$mentalMapId] = $mentalMap;
            }
        }

        $quizData = (new QuizDetailFetcher())->fetch(
            $student->id,
            $story->id,
            "UNIX_TIMESTAMP('$startDate')",
            "UNIX_TIMESTAMP('$finishDate')",
        );

        return $this->renderAjax('_detail', [
            'mentalMaps' => $mentalMaps,
            'data' => $data,
            'title' => $story->title . ' - ' . Yii::$app->formatter->asDate($date),
            'quizData' => $quizData,
        ]);
    }

    private function fetchStoriesProgress(int $studentId, int $userId, array $storyIds): array
    {
        if (count($storyIds) === 0) {
            return [];
        }

        $query = (new Query())
            ->select([
                new Expression("'edu' AS place"),
                'storyId' => 't.story_id',
                'completeTime' => 't.updated_at',
            ])
            ->from(['t' => 'story_student_progress'])
            ->where([
                't.student_id' => $studentId,
            ])
            ->andWhere(['in', 't.story_id', $storyIds])
            ->andWhere('t.progress = 100');

        $storyQuery = (new Query())
            ->select([
                new Expression("'story' AS place"),
                'storyId' => 't.story_id',
                'completeTime' => 't.updated_at',
            ])
            ->from(['t' => 'user_story_history'])
            ->where([
                't.user_id' => $userId,
            ])
            ->andWhere(['in', 't.story_id', $storyIds])
            ->andWhere('t.percent = 100');

        $rows = $query->union($storyQuery)->all();

        return array_combine(
            array_column($rows, 'storyId'),
            array_map(static function (array $row): array {
                return [
                    'place' => $row['place'],
                    'time' => $row['completeTime'],
                    'date' => SmartDate::dateSmart((int) $row['completeTime'], true),
                ];
            }, $rows),
        );
    }

    private function storyProgressTooltipText(array $data): string
    {
        $tip = 'История пройдена в обучении';
        if ($data['place'] === 'story') {
            $tip = 'История пройдена на сайте';
        }
        $tip .= ': ' . $data['date'];
        return '<span style="margin-left: 10px;" data-toggle="tooltip" class="glyphicon glyphicon-info-sign" title="' . $tip . '"></span>';
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    private function fetchStoryContentRestarts(
        int $studentId,
        int $storyId,
        DateTimeInterface $dateFrom,
        DateTimeInterface $dateTo
    ): string {
        $slideContent = (new StoryTestsFetcher())->fetch($storyId);

        $testRows = $this->fetchStoryTestRestarts(
            $studentId,
            $slideContent->find(SlideTest::class),
            $dateFrom,
            $dateTo,
        );

        $values = array_map(static function (array $value): string {
            return $value['testName'] . ' - ' . SmartDate::dateSmart($value['restartTime'], true);
        }, $testRows);

        $result = '';

        if (count($testRows) > 0) {
            $result = 'Тесты, начатые заново:' . PHP_EOL . implode(PHP_EOL, $values);
        }

        $mentalMapRows = $this->fetchStoryMentalMapRestarts(
            $studentId,
            $slideContent->find(SlideMentalMap::class),
            $dateFrom,
            $dateTo,
        );
        $values = array_map(static function (array $value): string {
            return $value['mentalMapName'] . ' - ' . SmartDate::dateSmart($value['restartTime'], true);
        }, $mentalMapRows);

        if (count($mentalMapRows) > 0) {
            if ($result !== '') {
                $result .= PHP_EOL;
            }
            $result .= 'Ментальные карты, начатые заново:' . PHP_EOL . implode(PHP_EOL, $values);
        }

        return $result;
    }

    private function fetchStoryTestRestarts(
        int $studentId,
        array $tests,
        DateTimeInterface $dateFrom,
        DateTimeInterface $dateTo
    ): array {
        if (count($tests) === 0) {
            return [];
        }
        $testIds = array_map(static function (SlideTest $test): int {
            return $test->getTestId();
        }, $tests);
        $betweenBegin = new Expression("UNIX_TIMESTAMP('{$dateFrom->format('Y-m-d H:i:s')}')");
        $betweenEnd = new Expression("UNIX_TIMESTAMP('{$dateTo->format('Y-m-d H:i:s')}')");
        $query = (new Query())
            ->select([
                'testId' => 't.test_id',
                'testName' => 'q.title',
                'restartTime' => 't.created_at',
            ])
            ->from(['t' => 'test_restart_log'])
            ->innerJoin(['q' => 'story_test'], 't.test_id = q.id')
            ->where([
                't.student_id' => $studentId,
            ])
            ->andWhere(['in', 't.test_id', $testIds])
            ->andWhere(['between', new Expression('t.created_at + (3 * 60 * 60)'), $betweenBegin, $betweenEnd]);
        return $query->all();
    }

    private function fetchStoryMentalMapRestarts(
        int $studentId,
        array $mentalMaps,
        DateTimeInterface $dateFrom,
        DateTimeInterface $dateTo
    ): array {
        if (count($mentalMaps) === 0) {
            return [];
        }
        $mentalMapIds = array_map(static function (SlideMentalMap $mentalMap): string {
            return $mentalMap->getMentalMapId();
        }, $mentalMaps);
        $betweenBegin = new Expression("UNIX_TIMESTAMP('{$dateFrom->format('Y-m-d H:i:s')}')");
        $betweenEnd = new Expression("UNIX_TIMESTAMP('{$dateTo->format('Y-m-d H:i:s')}')");
        $query = (new Query())
            ->select([
                'mentalMapId' => 't.mental_map_id',
                'mentalMapName' => 'm.name',
                'restartTime' => 't.created_at',
            ])
            ->from(['t' => 'mental_map_restart_log'])
            ->innerJoin(['m' => 'mental_map'], 't.mental_map_id = m.uuid')
            ->where([
                't.student_id' => $studentId,
            ])
            ->andWhere(['in', 't.mental_map_id', $mentalMapIds])
            ->andWhere(['between', new Expression('t.created_at + (3 * 60 * 60)'), $betweenBegin, $betweenEnd]);
        return $query->all();
    }
}

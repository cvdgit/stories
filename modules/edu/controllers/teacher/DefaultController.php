<?php

declare(strict_types=1);

namespace modules\edu\controllers\teacher;

use common\helpers\SmartDate;
use common\models\Story;
use common\models\UserStudent;
use common\rbac\UserRoles;
use common\services\TestDetailService;
use DateTimeImmutable;
use Exception;
use modules\edu\models\EduClassBook;
use modules\edu\models\EduClassProgram;
use modules\edu\query\EduProgramStoriesFetcher;
use modules\edu\query\GetStoryTests\Slide;
use modules\edu\query\GetStoryTests\SlideMentalMap;
use modules\edu\query\GetStoryTests\SlideRetelling;
use modules\edu\query\GetStoryTests\SlideTest;
use modules\edu\query\GetStoryTests\StoryTestsFetcher;
use modules\edu\query\StudentProgramLastActivityDateFetcher;
use modules\edu\Teacher\StudentsStat\DateStudentStatFetcher;
use modules\edu\widgets\StudentStatWidget;
use yii\base\InvalidConfigException;
use yii\db\Query;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\User as WebUser;

class DefaultController extends Controller
{
    /** @var TestDetailService */
    private $testDetailService;

    public function __construct($id, $module, TestDetailService $testDetailService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->testDetailService = $testDetailService;
    }

    public function actionIndex(WebUser $user): string
    {
        $teacherClassBookIds = (new Query())
            ->select(['classBookId' => 't.id'])
            ->from(['t' => 'edu_class_book'])
            ->where(['t.user_id' => $user->getId()])
            ->all();
        $teacherClassBookIds = array_column($teacherClassBookIds, 'classBookId');

        $accessClassBookIds = (new Query())
            ->select([
                'classBookId' => 't.class_book_id',
            ])
            ->from(['t' => 'edu_class_book_teacher_access'])
            ->where(['t.teacher_id' => $user->getId()])
            ->all();
        $accessClassBookIds = array_column($accessClassBookIds, 'classBookId');

        $classBookIds = array_merge($teacherClassBookIds, $accessClassBookIds);
        $classBooks = EduClassBook::find()->where(['in', 'id', $classBookIds])
            ->orderBy(['name' => SORT_ASC])
            ->all();
        return $this->render('index', [
            'classBooks' => $classBooks,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionClassProgramStats(int $class_book_id, int $class_program_id)
    {
        if (($classBook = EduClassBook::findOne($class_book_id)) === null) {
            throw new NotFoundHttpException('Класс не найден');
        }

        if (($classProgram = EduClassProgram::findOne($class_program_id)) === null) {
            throw new NotFoundHttpException('Программа не найдена');
        }

        $studentIds = array_map(static function ($student) {
            return $student->id;
        }, $classBook->students);

        $storiesData = (new EduProgramStoriesFetcher())->fetch($classBook->class_id, $classProgram->program_id);
        $storyIds = array_unique(array_column($storiesData, 'storyId'));
        $lastActivities = (new StudentProgramLastActivityDateFetcher())->fetch($studentIds, $storyIds);

        return $this->render('stats', [
            'classBook' => $classBook,
            'classProgram' => $classProgram,
            'lastActivities' => $lastActivities,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     * @throws InvalidConfigException
     * @throws BadRequestHttpException
     */
    public function actionStoryTesting(int $story_id, int $student_id, string $date): string
    {
        if (($story = Story::findOne($story_id)) === null) {
            throw new NotFoundHttpException('История не найдена');
        }

        if (($student = UserStudent::findOne($student_id)) === null) {
            throw new NotFoundHttpException('Ученик не найден');
        }

        try {
            $targetDate = new DateTimeImmutable($date);
        } catch (Exception $ex) {
            throw new BadRequestHttpException('Incorrect date');
        }

        $params = [
            'storyTitle' => $story->title,
            'studentName' => $student->getStudentName(),
            'date' => SmartDate::dateSmart(strtotime($date)),
        ];

        $historyData = (new DateStudentStatFetcher())->fetchTestings(
            $student->id,
            $story->id,
            $targetDate,
        );

        if (count($historyData) === 0) {
            return $this->renderAjax('_detail', array_merge($params, ['historyData' => []]));
        }

        return $this->renderAjax(
            '_detail',
            array_merge($params, ['historyData' => $historyData]),
        );
    }

    /**
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    public function actionStudentStats(int $id, int $class_program_id, int $class_book_id, WebUser $user): string
    {
        if (($student = UserStudent::findOne($id)) === null) {
            throw new NotFoundHttpException('Ученик не найден');
        }

        $classBook = EduClassBook::findOne($class_book_id);
        if ($classBook === null) {
            throw new NotFoundHttpException('Класс не найден');
        }

        $classProgram = null;
        if (($class_program_id !== null) && ($classProgram = EduClassProgram::findOne($class_program_id)) === null) {
            throw new NotFoundHttpException('Ученик не найден');
        }

        $class = $classBook->class;
        $classPrograms = $class->eduClassPrograms;

        if ($classProgram === null && count($classPrograms) > 0) {
            $classProgram = $classPrograms[0];
        }

        return $this->render('student_stats', [
            'classBook' => $classBook,
            'classProgram' => $classProgram,
            'student' => $student,
            'classPrograms' => $classPrograms,

            'statWidget' => StudentStatWidget::widget([
                'classProgram' => $classProgram,
                'classId' => $class->id,
                'student' => $student,
                'canClearHistory' => $user->can(UserRoles::ROLE_TEACHER),
            ]),
        ]);
    }

    public function actionDetail(int $test_id, int $student_id): string
    {
        $rows = $this->testDetailService->getDetail($test_id, $student_id);
        return $this->renderAjax('_detail', [
            'rows' => $rows,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     * @throws BadRequestHttpException
     */
    public function actionStoryMentalMaps(int $story_id, int $student_id, string $date): string
    {
        if (($story = Story::findOne($story_id)) === null) {
            throw new NotFoundHttpException('История не найдена');
        }

        if (($student = UserStudent::findOne($student_id)) === null) {
            throw new NotFoundHttpException('Ученик не найден');
        }

        try {
            $targetDate = new DateTimeImmutable($date);
        } catch (Exception $ex) {
            throw new BadRequestHttpException('Incorrect date');
        }

        $params = [
            'storyTitle' => $story->title,
            'studentName' => $student->getStudentName(),
            'date' => SmartDate::dateSmart(strtotime($date)),
        ];

        $historyData = (new DateStudentStatFetcher())->fetchMentalMaps(
            $student->user_id,
            $story->id,
            $targetDate,
        );

        if (count($historyData) === 0) {
            return $this->renderAjax('_mental_maps_history', array_merge($params, ['historyData' => []]));
        }

        return $this->renderAjax(
            '_mental_maps_history',
            array_merge($params, ['historyData' => $historyData]),
        );
    }

    /**
     * @throws NotFoundHttpException
     * @throws BadRequestHttpException
     * @throws InvalidConfigException
     */
    public function actionStoryDetail(int $story_id, int $student_id, string $date): string
    {
        if (($story = Story::findOne($story_id)) === null) {
            throw new NotFoundHttpException('История не найдена');
        }

        if (($student = UserStudent::findOne($student_id)) === null) {
            throw new NotFoundHttpException('Ученик не найден');
        }

        try {
            $targetDate = new DateTimeImmutable($date);
        } catch (Exception $ex) {
            throw new BadRequestHttpException('Incorrect date');
        }

        $statFetcher = new DateStudentStatFetcher();
        $historyRows = $statFetcher->fetchSlides(
            $student->id,
            $story->id,
            $targetDate,
        );

        $slideContent = (new StoryTestsFetcher())->fetch($story->id);
        $slides = $slideContent->getContents([
            Slide::class,
            SlideMentalMap::class,
            SlideTest::class,
            SlideRetelling::class,
        ]);

        $mentalMapHistory = $statFetcher->fetchMentalMaps(
            $student->user_id,
            $story->id,
            $targetDate,
        );

        $testsHistory = $statFetcher->fetchTestings(
            $student->id,
            $story->id,
            $targetDate
        );

        $history = [];
        foreach ($slides as $slide) {
            $slideId = $slide->getSlideId();
            $type = get_class($slide);
            $historyItem = [
                'slideNumber' => $slide->getSlideNumber(),
                'type' => $type,
                'history' => array_values(
                    array_filter($historyRows, static function (array $row) use ($slideId): bool {
                        return (int) $row['slide_id'] === $slideId;
                    }),
                ),
            ];
            if (count($historyItem['history']) === 0) {
                continue;
            }

            $historyItem['previouslyViewed'] = false; // count($historyItem['history']) > 1;

            if ($type === Slide::class) {
                $historyItem['slide'] = $slide->getContent();
            }
            if ($type === SlideMentalMap::class) {
                /** @var SlideMentalMap $slide */
                $historyItem['mentalMaps'] = array_values(
                    array_filter($mentalMapHistory, static function (array $row) use ($slide): bool {
                        return $row['id'] === $slide->getMentalMapId();
                    }),
                );
            }
            if ($type === SlideTest::class) {
                /** @var SlideTest $slide */
                $historyItem['tests'] = array_values(
                    array_filter($testsHistory, static function (array $row) use ($slide): bool {
                        return $row['testId'] === $slide->getTestId();
                    }),
                );
            }
            $history[] = $historyItem;
        }

        return $this->renderAjax('_story_detail', [
            'history' => $history,
        ]);
    }
}

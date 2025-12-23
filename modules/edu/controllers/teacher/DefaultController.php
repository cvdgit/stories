<?php

declare(strict_types=1);

namespace modules\edu\controllers\teacher;

use common\components\MentalMapThreshold;
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
use modules\edu\query\StudentProgramLastActivityDateFetcher;
use modules\edu\widgets\StudentStatWidget;
use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
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
     */
    public function actionStoryTesting(int $story_id, int $student_id): array
    {
        $this->response->format = Response::FORMAT_JSON;

        if (($story = Story::findOne($story_id)) === null) {
            throw new NotFoundHttpException('История не найдена');
        }

        if (($student = UserStudent::findOne($student_id)) === null) {
            throw new NotFoundHttpException('Ученик не найден');
        }

        $testings = array_map(function ($testing) use ($student) {
            return [
                'id' => $testing->id,
                'name' => $testing->header,
                'incorrect' => $this->testDetailService->getIncorrectCount($testing->id, $student->id),
                'resource' => Url::to(
                    ['/edu/teacher/default/detail', 'test_id' => $testing->id, 'student_id' => $student->id],
                ),
                'progress' => $student->getProgress($testing->id),
            ];
        }, $story->tests);

        return ['success' => true, 'data' => $testings];
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

    public function actionDetail(int $test_id, int $student_id)
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
            $targetDate = (new DateTimeImmutable($date))->format('Y-m-d');
            $betweenBegin = new Expression("UNIX_TIMESTAMP('$targetDate 00:00:00')");
            $betweenEnd = new Expression("UNIX_TIMESTAMP('$targetDate 23:59:59')");
        } catch (Exception $ex) {
            throw new BadRequestHttpException('Incorrect date');
        }

        $query = (new Query())
            ->select('*')
            ->from(['h' => 'mental_map_history'])
            ->where([
                'h.user_id' => $student->user_id,
                'h.story_id' => $story->id,
            ])
            ->andWhere(['between', new Expression('h.created_at + (3 * 60 * 60)'), $betweenBegin, $betweenEnd])
            ->orderBy(['h.created_at' => SORT_ASC]);
        $mentalMapHistoryData = $query->all();

        $mentalMapIds = array_column($mentalMapHistoryData, 'mental_map_id');

        $params = [
            'storyTitle' => $story->title,
            'studentName' => $student->getStudentName(),
            'date' => SmartDate::dateSmart(strtotime($date)),
        ];

        if (count($mentalMapIds) === 0) {
            return $this->renderAjax('_mental_maps_history', array_merge($params, ['historyData' => []]));
        }

        return $this->renderAjax(
            '_mental_maps_history',
            array_merge(
                $params,
                [
                    'historyData' => array_map(
                        static function(array $row): array {
                            $payload = Json::decode($row['payload'] ?? '[]');
                            $userResponse = $payload['user_response'] ?? $row['content'];

                            $threshold = $row['threshold'] ?? MentalMapThreshold::getDefaultThreshold(Yii::$app->params);
                            $threshold = (int) $threshold;

                            $userSimilarity = (int) $row['overall_similarity'];
                            $correct = $userSimilarity >= $threshold;

                            $allImportantWordsIncluded = $row['all_important_words_included'];
                            if ($allImportantWordsIncluded !== null && $correct) {
                                $correct = (int) $allImportantWordsIncluded === 1;
                            }
                            return [
                                'createdAt' => SmartDate::dateSmart($row['created_at'], true),
                                'userResponse' => $userResponse,
                                'correct' => $correct,
                                'detail' => [
                                    'threshold' => $threshold,
                                    'userSimilarity' => $userSimilarity,
                                ],
                            ];
                        },
                        $mentalMapHistoryData
                    ),
                ],
            ),
        );
    }
}

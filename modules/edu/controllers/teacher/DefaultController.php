<?php

declare(strict_types=1);

namespace modules\edu\controllers\teacher;

use common\models\Story;
use common\models\UserStudent;
use common\services\TestDetailService;
use modules\edu\models\EduClassBook;
use modules\edu\models\EduClassProgram;
use modules\edu\query\EduProgramStoriesFetcher;
use modules\edu\query\StudentProgramLastActivityDateFetcher;
use modules\edu\widgets\StudentStatWidget;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class DefaultController extends Controller
{
    /** @var TestDetailService */
    private $testDetailService;

    public function __construct($id, $module, TestDetailService $testDetailService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->testDetailService = $testDetailService;
    }

    public function actionIndex()
    {
        $classBooks = EduClassBook::findTeacherClassBooks(Yii::$app->user->getId())
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

        $studentIds = array_map(static function($student) {
            return $student->id;
        }, $classBook->students);

        $storiesData = (new EduProgramStoriesFetcher())->fetch($classBook->class_id, $classProgram->program_id);
        $storyIds = array_unique(array_column($storiesData, 'storyId'));
        $lastActivities = (new StudentProgramLastActivityDateFetcher())->fetch($studentIds, $storyIds);

        return $this->render('stats', [
            'classBook' => $classBook,
            'classProgram' => $classProgram,
            'lastActivities'  => $lastActivities,
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

        $testings = array_map(function($testing) use ($student) {
            return [
                'id' => $testing->id,
                'name' => $testing->header,
                'incorrect' => $this->testDetailService->getIncorrectCount($testing->id, $student->id),
                'resource' => Url::to(['/edu/teacher/default/detail', 'test_id' => $testing->id, 'student_id' => $student->id]),
                'progress' => $student->getProgress($testing->id)
            ];
        }, $story->tests);

        return ['success' => true, 'data' => $testings];
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionStudentStats(int $id, int $class_program_id, int $class_book_id): string
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
}

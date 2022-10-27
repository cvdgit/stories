<?php

declare(strict_types=1);


namespace modules\edu\controllers\teacher;

use common\models\Story;
use common\models\UserStudent;
use modules\edu\models\EduClassBook;
use modules\edu\models\EduClassProgram;
use modules\edu\query\EduProgramStoriesFetcher;
use modules\edu\query\StudentProgramLastActivityDateFetcher;
use modules\edu\query\StudentQuestionFetcher;
use modules\edu\query\StudentStatsFetcher;
use modules\edu\query\StudentStoryStatByDateFetcher;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class DefaultController extends Controller
{

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

        $studentIds = array_map(static function($student){ return $student->id; }, $classBook->students);
        $storyIds = (new EduProgramStoriesFetcher())->fetch($classBook->class_id, $classProgram->id);
        $lastActivities = (new StudentProgramLastActivityDateFetcher())->fetch($studentIds, array_keys($storyIds));

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

        $testings = array_map(static function($testing) use ($student) {
            return [
                'id' => $testing->id,
                'name' => $testing->header,
                'resource' => Url::to(['/test/detail', 'test_id' => $testing->id, 'student_id' => $student->id]),
                'progress' => $student->getProgress($testing->id)
            ];
        }, $story->tests);

        return ['success' => true, 'data' => $testings];
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionStudentStats(int $id, int $class_program_id): string
    {

        if (($student = UserStudent::findOne($id)) === null) {
            throw new NotFoundHttpException('Ученик не найден');
        }

        $classProgram = null;
        if (($class_program_id !== null) && ($classProgram = EduClassProgram::findOne($class_program_id)) === null) {
            throw new NotFoundHttpException('Ученик не найден');
        }

        $class = $student->class;
        $classPrograms = $class->eduClassPrograms;

        if ($classProgram === null && count($classPrograms) > 0) {
            $classProgram = $classPrograms[0];
        }

        $programStoriesData = (new EduProgramStoriesFetcher())->fetch($class->id, $classProgram->program_id);
        $storyIds = array_column($programStoriesData, 'storyId');

        $storyModels = Story::find()
            ->where(['in', 'id', $storyIds])
            ->indexBy('id')
            ->all();

        $statData = (new StudentStoryStatByDateFetcher())->fetch($student->id, $storyIds);

        $stat = (new StudentStatsFetcher())->fetch($statData, $programStoriesData);

        return $this->render('student_stats', [
            'classProgram' => $classProgram,
            'classPrograms' => $classPrograms,
            'student' => $student,
            'stat' => $stat,
            'storyModels' => $storyModels,
            'questionFetcher' => new StudentQuestionFetcher(),
        ]);
    }
}

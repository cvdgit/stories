<?php

namespace modules\edu\controllers;

use common\models\User;
use common\rbac\UserRoles;
use Exception;
use modules\edu\models\EduStory;
use modules\edu\models\EduStudent;
use modules\edu\query\GetStoryTests\StoryTestsFetcher;
use modules\edu\query\StoryStudentProgressFetcher;
use modules\edu\query\StudentClassFetcher;
use modules\edu\services\StudentStatService;
use Ramsey\Uuid\Uuid;
use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Cookie;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\User as WebUser;

/**
 * Default controller for the `edu` module
 */
class DefaultController extends Controller
{
    /**
     * @var StudentStatService
     */
    private $studentStatService;
    private $studentClassFetcher;

    public function __construct($id, $module, StudentStatService $studentStatService, StudentClassFetcher $studentClassFetcher, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->studentStatService = $studentStatService;
        $this->studentClassFetcher = $studentClassFetcher;
    }

    public function actionIndex(): Response
    {
        if (Yii::$app->user->can(UserRoles::ROLE_ADMIN)) {
            return $this->redirect(['/edu/parent/default/index']);
        }

        $student = Yii::$app->studentContext->getStudent();

        if (Yii::$app->user->can(UserRoles::ROLE_TEACHER)) {
            if ($student === null) {
                return $this->redirect(['/edu/teacher/default/index']);
            }
            return $this->redirect(['/edu/student/index']);
        }

        if (Yii::$app->user->can(UserRoles::ROLE_STUDENT)) {
            return $this->redirect(['/edu/student/index']);
        }

        if ($student === null) {
            return $this->redirect(['/edu/parent/default/index']);
        }

        /*
        $students = $currentUser->students;
        if (count($students) === 0) {
            return $this->redirect(['/edu/parent/index']);
        }

        $readCookies = Yii::$app->request->cookies;
        $uidCookie = $readCookies->getValue('uid');

        if ($uidCookie === null) {

            $firstStudent = $students[0];

            $writeCookies = $this->response->cookies;
            $uid = Uuid::uuid4()->toString();
            $writeCookies->add(new Cookie([
                'name' => 'uid',
                'value' => $uid,
            ]));

            Yii::$app->db->createCommand()
                ->insert('user_student_session', [
                    'uid' => $uid,
                    'user_id' => $currentUser->id,
                    'student_id' => $firstStudent->id,
                ])
                ->execute();

            return $this->redirect(['/edu/student/index']);
        }

        $sessionRow = (new Query())
            ->select('*')
            ->from('user_student_session')
            ->where('uid = :uid', [':uid' => $uidCookie])
            ->one();

        if ($sessionRow === false) {

        }*/

        return $this->redirect(['/edu/student/index']);
    }

    public function actionSwitchToParent()
    {

        $readCookies = Yii::$app->request->cookies;

        $writeCookies = $this->response->cookies;
        if ($writeCookies->has('uid')) {
            $uidCookie = $writeCookies->get('uid');
            $writeCookies->remove($uidCookie);
        }

        $uid = Uuid::uuid4()->toString();
        $writeCookies->add(new Cookie([
            'name' => 'uid',
            'value' => $uid,
        ]));

        /** @var User $currentUser */
        $currentUser = Yii::$app->user->identity;

        $mainStudent = $currentUser->student();

        Yii::$app->db->createCommand()
            ->insert('user_student_session', [
                'uid' => $uid,
                'user_id' => $currentUser->id,
                'student_id' => $mainStudent->id,
            ])
            ->execute();

        return $this->redirect(['/edu/parent/index']);
    }

    /**
     * @throws ForbiddenHttpException
     */
    public function actionGetNextStory(int $story_id, int $program_id, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;

        $student = Yii::$app->studentContext->getStudent();
        if ($student === null) {
            $student = Yii::$app->user->identity->student();
            if ($student === null) {
                throw new ForbiddenHttpException('Доступ запрещен');
            }
        }

        $studentClassBookId = $this->studentClassFetcher->fetch($student->id);
        $programTopics = (new Query())
            ->select(['topicId' => 't.id'])
            ->from(['t' => 'edu_topic'])
            ->innerJoin(['acc' => 'edu_class_book_topic_access'], 'acc.topic_id = t.id')
            ->where([
                't.class_program_id' => $program_id,
                'acc.class_book_id' => $studentClassBookId
            ])
            ->andWhere('acc.class_program_id = t.class_program_id')
            ->all();
        $programTopicIds = array_column($programTopics, 'topicId');

        $query = (new Query())
            ->select([
                'storyId' => 'els.story_id',
            ])
            ->from(['et' => 'edu_topic'])
            ->innerJoin(['el' => 'edu_lesson'], 'et.id = el.topic_id')
            ->innerJoin(['els' => 'edu_lesson_story'], 'el.id = els.lesson_id')
            ->where(['in', 'et.id', $programTopicIds])
            ->orderBy(['et.`order`' => SORT_ASC, 'el.`order`' => SORT_ASC, 'els.`order`' => SORT_ASC]);
        $rows = $query->all();

        $nextStoryId = null;
        foreach ($rows as $i => $row) {

            if ($story_id === (int) $row['storyId']) {
                $nextIndex = $i + 1;
                if (isset($rows[$nextIndex])) {
                    $nextStoryId = $rows[$nextIndex]['storyId'];
                    break;
                }
            }
        }

        if ($nextStoryId === null) {
            return ['success' => true, 'url' => Url::to(['/edu/student/index'])];
        }
        return ['success' => true, 'url' => Url::to(['/edu/story/view', 'id' => $nextStoryId, 'program_id' => $program_id])];
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionStoryStat(int $story_id, int $student_id, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;

        if (($story = EduStory::findOne($story_id)) === null) {
            throw new NotFoundHttpException('История не найдена');
        }

        $student = EduStudent::findOne($student_id);
        if ($student === null) {
            throw new NotFoundHttpException('Ученик не найден');
        }

        $storyTests = (new StoryTestsFetcher())->fetch($story->id);
        $storyTestIds = array_map(static function ($item) {
            return $item->getTestId();
        }, $storyTests);

        $progress = (new StoryStudentProgressFetcher())->fetch($story->id, $student->id);

        $testingRows = (new Query())
            ->select([
                'test_id' => new Expression('DISTINCT story_test.id'),
                'test_name' => 'story_test.header',
                'progress' => new Expression('IFNULL(student_question_progress.progress, 0)'),
            ])
            ->from('story_test')
            ->leftJoin('student_question_progress', 'student_question_progress.test_id = story_test.id AND student_question_progress.student_id = :student', [':student' => $student->id])
            ->where(['in', 'story_test.id', $storyTestIds])
            ->all();

        $testSlides = array_combine(
            array_map(static function ($item) { return $item->getTestId(); }, $storyTests),
            array_map(static function ($item) { return $item->getSlideNumber(); }, $storyTests)
        );

        $testingRows = array_map(static function ($row) use ($testSlides) {
            $row['slide_number'] = $testSlides[$row['test_id']];
            return $row;
        }, $testingRows);

        $data = [
            'is_complete' => $story->isComplete($progress),
            'progress' => $progress,
            'tests' => $testingRows,
        ];

        return ['success' => true, 'data' => $data];
    }

    /**
     * @throws ForbiddenHttpException
     */
    public function actionClearStoryHistory(int $student_id, int $story_id, WebUser $user): Response
    {
        if (!$user->can(UserRoles::ROLE_TEACHER)) {
            throw new ForbiddenHttpException('Доступ запрещен');
        }
        try {
            $this->studentStatService->clearStoryHistory($student_id, $story_id);
            Yii::$app->session->setFlash('success', 'Успешно');
        } catch (Exception $exception) {
            Yii::$app->errorHandler->logException($exception);
            Yii::$app->session->setFlash('error', $exception->getMessage());
        }
        return $this->redirect($this->request->referrer);
    }
}

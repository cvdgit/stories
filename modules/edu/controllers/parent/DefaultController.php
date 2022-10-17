<?php

declare(strict_types=1);

namespace modules\edu\controllers\parent;

use common\models\Story;
use common\models\User;
use common\models\UserStudent;
use Exception;
use modules\edu\components\StudentLoginGenerator;
use modules\edu\forms\student\StudentForm;
use modules\edu\models\EduClassProgram;
use modules\edu\models\EduParentInvite;
use modules\edu\models\EduUser;
use modules\edu\query\EduProgramStoriesFetcher;
use modules\edu\query\StudentStoryStatByDateFetcher;
use modules\edu\services\StudentService;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;

class DefaultController extends Controller
{

    private $studentService;

    public function __construct($id, $module, StudentService $studentService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->studentService = $studentService;
    }

    public function actionIndex(): string
    {

        /** @var EduUser $currentUser */
        $currentUser = EduUser::findOne(Yii::$app->user->getId());

        $dataProvider = new ActiveDataProvider([
            'query' => $currentUser->getStudents(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreateStudent(Request $request)
    {
        $formModel = new StudentForm();
        if ($formModel->load($request->post())) {

            if (!$formModel->validate()) {
                throw new \DomainException('Ошибка валидации');
            }

            try {
                $this->studentService->createStudentByParent(Yii::$app->user->getId(), EduUser::createUsername(), $formModel, StudentLoginGenerator::generateLogin(), StudentLoginGenerator::generatePassword());
                Yii::$app->session->setFlash('success', 'Ученик успешно создан');
                return $this->redirect(['index']);
            }
            catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                Yii::$app->session->setFlash('error', 'Ошибка при создании ученика');
            }
        }
        return $this->render('create-student', [
            'formModel' => $formModel,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionUpdateStudent(int $id)
    {
        if (($student = UserStudent::findOne($id)) === null) {
            throw new NotFoundHttpException('Ученик не найден');
        }

        $studentForm = new StudentForm($student);
        if ($this->request->isPost && $studentForm->load($this->request->post())) {
            try {
                $this->studentService->updateStudent($student, $studentForm);
                Yii::$app->session->setFlash('success', 'Ученик успешно изменен');
                return $this->redirect(['index']);
            }
            catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                Yii::$app->session->setFlash('error', $exception->getMessage());
            }
        }

        return $this->render('update-student', [
            'formModel' => $studentForm,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionStats(int $id, int $class_program_id = null): string
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

        $stat = [];

        $programStoriesData = (new EduProgramStoriesFetcher())->fetch($class->id, $classProgram->id);
        $storyIds = array_column($programStoriesData, 'storyId');

        $storyModels = Story::find()
            ->where(['in', 'id', $storyIds])
            ->indexBy('id')
            ->all();

        $statData = (new StudentStoryStatByDateFetcher())->fetch($student->id, $storyIds);

        foreach ($statData as $statItem) {

            $item = [
                'date' => $statItem['targetDate'],
                'topics' => [],
            ];

            $topics = [];
            foreach (explode(',', $statItem['storyIds']) as $storyId) {

                $storyData = $programStoriesData[$storyId];

                if (!isset($topics[$storyData['topicId']])) {
                    $topics[$storyData['topicId']] = [
                        'topicId' => $storyData['topicId'],
                        'topicName' => $storyData['topicName'],
                        'lessons' => [],
                    ];
                }

                $topicLessonIds = array_column($topics[$storyData['topicId']]['lessons'],'lessonId');
                if (!in_array($storyData['lessonId'], $topicLessonIds, true)) {
                    $lessonItem = [
                        'lessonId' => $storyData['lessonId'],
                        'lessonName' => $storyData['lessonName'],
                        'stories' => [],
                    ];
                    $topics[$storyData['topicId']]['lessons'][$storyData['lessonId']] = $lessonItem;
                }

                $topics[$storyData['topicId']]['lessons'][$storyData['lessonId']]['stories'][] = $storyModels[$storyId];
            }

            $item['topics'] = $topics;

            $stat[] = $item;
        }

        return $this->render('stats', [
            'classProgram' => $classProgram,
            'classPrograms' => $classPrograms,
            'student' => $student,
            'stat' => $stat,
        ]);
    }

    public function actionInvite(string $code): Response
    {
        try {
            if (Yii::$app->user->isGuest) {
                throw new ForbiddenHttpException('Необходимо авторизоваться');
            }

            if (($invite = EduParentInvite::findOne(['code' => $code])) === null) {
                throw new NotFoundHttpException('Приглашение не найдено');
            }

            if (($user = EduUser::findOne(['email' => $invite->email])) === null) {
                throw new NotFoundHttpException('Пользователь не найден');
            }

            $currentUserEmail = Yii::$app->user->identity->email;
            if (!$invite->isOwnerEmail($currentUserEmail)) {
                throw new ForbiddenHttpException('Отказано в доступе');
            }

            $this->studentService->setStudentParent($user->id, $invite);

            Yii::$app->session->setFlash('success', 'Операция выполнена успешно');
            return $this->redirect(['/edu/parent/default/index']);
        }
        catch (Exception $exception) {
            Yii::$app->session->setFlash('error', $exception->getMessage());
            return $this->redirect(['/']);
        }
    }
}

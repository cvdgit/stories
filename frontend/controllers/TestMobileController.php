<?php

namespace frontend\controllers;

use backend\components\BaseController;
use backend\components\training\base\Serializer;
use backend\components\training\base\TestParams;
use backend\components\training\base\UserProgress;
use backend\components\training\collection\TestBuilder;
use backend\components\training\neo\NeoTestBuilder;
use backend\services\NeoQueryService;
use common\helpers\UserHelper;
use common\models\StoryTest;
use common\models\test\SourceType;
use common\models\User;
use common\models\UserQuestionHistoryModel;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class TestMobileController extends BaseController
{

    private $neoQueryService;

    public function __construct($id, $module, NeoQueryService $neoQueryService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->neoQueryService = $neoQueryService;
    }

    public function actionView(int $id, int $student_id = null)
    {
        if (($model = StoryTest::findOne($id)) === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $this->render('view', [
            'model' => $model,
            'studentId' => $student_id,
        ]);
    }

    private function getStudents(int $testID): array
    {
        $students = [];
        if (!Yii::$app->user->isGuest) {
            $user = Yii::$app->user->identity;
            foreach ($user->students as $student) {
                $students[] = [
                    'id' => $student->id,
                    'name' => $student->isMain() ? $student->user->getProfileName() : $student->name,
                    'progress' => (int)$student->getProgress($testID),
                ];
            }
        }
        return $students;
    }

    public function actionGetData(int $test_id, int $student_id = null, $question_params = null, bool $fast_mode = false)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (($model = StoryTest::findOne($test_id)) === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $repeat = $fast_mode ? 1 : 5;

        $userProgress = new UserProgress();
        if ($student_id !== null && !$fast_mode) {
            $userQuestionHistoryModel = new UserQuestionHistoryModel();
            $userQuestionHistoryModel->student_id = $student_id;
            $userProgress->setHistory($userQuestionHistoryModel->getUserQuestionHistoryLocal($model->id, $repeat));
            $userProgress->setStars($userQuestionHistoryModel->getUserQuestionHistoryStarsLocal($model->id));
            $userProgress->setStarsCount($userQuestionHistoryModel->getUserHistoryStarsCountLocal($model->id));
        }

        if (SourceType::isNeo($model)) {

            $questionId = $model->question_list_id;
            $questionParams = null;
            if ($model->question_params !== null) {
                $questionParams = base64_encode($model->question_params);
            }
            $wrongAnswersParams = null;
            if (!empty($model->wrong_answers_params)) {
                $wrongAnswersParams = urlencode(base64_encode($model->wrong_answers_params));
            }
            $result = $this->neoQueryService->query($questionId, $questionParams, $wrongAnswersParams);

            $testParams = new TestParams($model->id, $model->source, $model->incorrect_answer_text);
            return (new NeoTestBuilder($result, $userProgress, $repeat))
                ->build($this->getStudents($model->id), $testParams);
        }

        $collection = (new TestBuilder($model, $model->getQuestionDataMobile($userProgress->getHistory()), $model->getQuestionDataCountMobile(), $userProgress->getStars(), $fast_mode))
            ->build();
        return (new Serializer())
            ->serialize($model, $collection, [], $userProgress->getStarsCount(), $fast_mode);
    }

    public function actionInit(int $test_id, int $user_id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (($model = StoryTest::findOne($test_id)) === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $user = User::findOne($user_id);
        if ($user === null && !Yii::$app->user->isGuest) {
            $user = Yii::$app->user->identity;
        }

        return [
            'test' => [
                'header' => $model->header,
                'description' => $model->description_text,
                'remote' => $model->isRemote(),
            ],
            'students' => UserHelper::getUserStudents($model, $user),
        ];
    }
}
<?php

namespace frontend\controllers;

use backend\services\StoryEditorService;
use common\helpers\UserHelper;
use common\models\Category;
use common\models\Story;
use common\models\StoryTest;
use common\models\StudentQuestionProgress;
use common\models\UserQuestionHistory;
use common\models\UserStudent;
use common\services\TestDetailService;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class TestController extends Controller
{

    private $storyEditorService;
    private $testDetailService;

    public function __construct($id, $module, StoryEditorService $storyEditorService, TestDetailService $testDetailService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->storyEditorService = $storyEditorService;
        $this->testDetailService = $testDetailService;
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex(int $category_id, $student_id = null)
    {
        $model = $this->findCategoryModel($category_id);
        $query = Story::findPublishedStories()
            ->innerJoinWith(['categories', 'tests'])
            ->distinct()
            ->andFilterWhere(['in', 'category.id', $model->subCategories()]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => ['published_at' => SORT_DESC],
            ],
        ]);
        $this->getView()->setMetaTags('Тесты', 'Тесты', 'Тесты', 'Тесты');
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'students' => UserHelper::getStudents(),
            'activeStudent' => UserHelper::getStudent($student_id),
            'category' => $model,
        ]);
    }

    private function findCategoryModel(int $id)
    {
        if (($model = Category::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    private function findStudentModel($id)
    {
        if (($model = UserStudent::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    private function findTestModel($id)
    {
        if (($model = StoryTest::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionClearHistory(int $category_id, int $student_id, int $test_id)
    {
        $categoryModel = $this->findCategoryModel($category_id);
        $studentModel = $this->findStudentModel($student_id);
        $testModel = $this->findTestModel($test_id);
        UserQuestionHistory::clearTestHistory($studentModel->id, $testModel->id);
        StudentQuestionProgress::resetProgress($studentModel->id, $testModel->id);
        Yii::$app->session->setFlash('success', 'История прохождения теста удалена');
        return $this->redirect(['index', 'category_id' => $categoryModel->id, 'student_id' => $studentModel->id]);
    }

    public function actionView(int $id)
    {
        $model = $this->findTestModel($id);
        return $this->renderAjax('view', [
            'model' => $model,
        ]);
    }

    public function actionViewByUser(int $id, int $user_id)
    {
        $model = $this->findTestModel($id);
        return $this->renderAjax('view-by-user', [
            'model' => $model,
            'userId' => $user_id,
        ]);
    }

    public function actionDetail(int $test_id, int $student_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $rows = $this->testDetailService->getDetail($test_id, $student_id);
        return $this->renderAjax('_detail', [
            'rows' => $rows,
        ]);
    }
}

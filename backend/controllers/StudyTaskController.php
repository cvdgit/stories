<?php

namespace backend\controllers;

use backend\components\StudyController;
use backend\models\study_task\CreateStudyTaskForm;
use backend\models\study_task\StudyTaskAssignForm;
use backend\models\study_task\UpdateStudyTaskForm;
use common\services\TestDetailService;
use Yii;
use common\models\StudyTask;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * StudyTaskController implements the CRUD actions for StudyTask model.
 */
class StudyTaskController extends StudyController
{

    private $testDetailService;

    public function __construct($id, $module, TestDetailService $testDetailService, $config = [])
    {
        $this->testDetailService = $testDetailService;
        parent::__construct($id, $module, $config);
    }

    public function behaviors()
    {
        return array_merge([
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ], parent::behaviors());
    }

    /**
     * Lists all StudyTask models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => StudyTask::find()->with(['createdBy', 'updatedBy']),
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new StudyTask model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CreateStudyTaskForm();
        if ($model->load(Yii::$app->request->post())) {
            $model->createTask(Yii::$app->user->getId());
            Yii::$app->session->setFlash('success', 'Задание успешно создано');
            return $this->redirect(['index']);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing StudyTask model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate(int $id)
    {
        /** @var StudyTask $taskModel */
        $taskModel = $this->findModel(StudyTask::class, $id);
        $model = new UpdateStudyTaskForm($taskModel);
        if ($model->load(Yii::$app->request->post())) {
            $model->updateTask();
            Yii::$app->session->setFlash('success', 'Задание успешно обновлено');
            return $this->refresh();
        }
        $assignDataProvider = new ActiveDataProvider([
            'query' => $taskModel->getStudyGroups(),
        ]);
        return $this->render('update', [
            'model' => $model,
            'assignDataProvider' => $assignDataProvider
        ]);
    }

    /**
     * Deletes an existing StudyTask model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete(int $id)
    {
        $model = $this->findModel(StudyTask::class, $id);
        $model->delete();
        return $this->redirect(['index']);
    }

    public function actionAssign()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $form = new StudyTaskAssignForm();
        if ($form->load(Yii::$app->request->post())) {
            $form->assign();
            Yii::$app->session->setFlash('success', 'Задание успешно назначено');
            return ['success' => true];
        }
        return ['success' => false];
    }

    public function actionTestDetail(int $task_id, int $student_id)
    {
        /** @var StudyTask $taskModel */
        $taskModel = $this->findModel(StudyTask::class, $task_id);
        $storyModel = $taskModel->story;
        $testModel = $storyModel->storyStoryTests[0];
        $rows = $this->testDetailService->getDetail($testModel->id, $student_id);
        return $this->renderAjax('_test_detail', [
            'rows' => $rows,
        ]);
    }
}

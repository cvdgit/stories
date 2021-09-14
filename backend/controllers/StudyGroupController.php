<?php

namespace backend\controllers;

use backend\components\StudyController;
use backend\models\study_group\ImportUsersFromTextForm;
use common\models\StoryStatistics;
use common\models\StoryStoryTest;
use common\models\StudyGroupUser;
use common\models\StudyTask;
use common\models\StudyTaskAssign;
use common\models\StudyTaskProgress;
use common\models\User;
use common\models\UserQuestionHistory;
use common\models\UserStudent;
use common\services\UserService;
use Yii;
use common\models\StudyGroup;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use yii\db\Query;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * StudyGroupController implements the CRUD actions for StudyGroup model.
 */
class StudyGroupController extends StudyController
{

    private $userService;

    public function __construct($id, $module, UserService $userService, $config = [])
    {
        $this->userService = $userService;
        parent::__construct($id, $module, $config);
    }

    public function behaviors()
    {
        return array_merge([
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'delete-user-item' => ['POST'],
                ],
            ],
        ], parent::behaviors());
    }

    /**
     * Lists all StudyGroup models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => StudyGroup::find(),
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new StudyGroup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new StudyGroup();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Группа успешно создана');
            return $this->redirect(['update', 'id' => $model->id]);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing StudyGroup model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate(int $id)
    {
        /** @var StudyGroup $model */
        $model = $this->findModel(StudyGroup::class, $id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Изменения успешно сохранена');
            return $this->refresh();
        }

        $usersDataProvider = new ActiveDataProvider([
            'query' => $model->getUsers(),
        ]);

        return $this->render('update', [
            'model' => $model,
            'usersDataProvider' => $usersDataProvider,
        ]);
    }

    /**
     * Deletes an existing StudyGroup model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete(int $id)
    {
        /** @var StudyGroup $model */
        $model = $this->findModel(StudyGroup::class, $id);
        $model->delete();
        return $this->redirect(['index']);
    }

    public function actionImportUsersFromText()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $form = new ImportUsersFromTextForm();
        if ($form->load(Yii::$app->request->post())) {
            try {
                $importData = $form->import();
                $result = $this->userService->createFromGroup($importData);
                $form->createGroupUsers($result);
                Yii::$app->session->setFlash('success', 'Импорт успешно завершен');
                return ['success' => true];
            }
            catch (\Exception $ex) {
                return ['success' => false, 'error' => $ex->getMessage()];
            }
        }
        return ['success' => true];
    }

    public function actionDeleteUserItem(int $group_id, int $user_id)
    {
        $model = StudyGroupUser::findItem($group_id, $user_id);
        $model->delete();
        Yii::$app->session->setFlash('success', 'Запись успешно удалена');
        return $this->redirect(['update', 'id' => $group_id]);
    }

    public function actionAssignedTasks(int $group_id)
    {
        /** @var StudyGroup $groupModel */
        $groupModel = $this->findModel(StudyGroup::class, $group_id);
        $dataProvider = new ActiveDataProvider([
            'query' => $groupModel->getStudyTasks(),
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
        ]);
        return $this->render('assigned_tasks', [
            'groupModel' => $groupModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionTaskUsers(int $group_id, int $task_id)
    {
        if (($assignModel = StudyTaskAssign::findOne([$task_id, $group_id])) === null) {
            throw new NotFoundHttpException('Not found');
        }
        $timeQuery = (new Query())
            ->select(['SUM(time_stat.begin_time + time_stat.end_time)'])
            ->from(['time_stat' => StoryStatistics::tableName()])
            ->where('time_stat.story_id = t5.story_id AND time_stat.user_id = t3.id');
        $testMistakesQuery = (new Query())
            ->select(['COUNT(question_history.id)'])
            ->from(['story_test' => StoryStoryTest::tableName()])
            ->innerJoin(['question_history' => UserQuestionHistory::tableName()], 'question_history.test_id = story_test.test_id')
            ->where('story_test.story_id = t5.story_id')
            ->andWhere('question_history.student_id = t6.id')
            ->andWhere('question_history.correct_answer = 0');
        $query = (new Query())
            ->select([
                'username' => 't3.username',
                'assign_date' => 't.created_at',
                'begin_date' => 't4.created_at',
                'task_status' => 't4.status',
                'total_time' => $timeQuery,
                'total_mistake' => $testMistakesQuery,
            ])
            ->from(['t' => StudyTaskAssign::tableName()])
            ->innerJoin(['t2' => StudyGroupUser::tableName()], 't.study_group_id = t2.study_group_id')
            ->innerJoin(['t3' => User::tableName()], 't2.user_id = t3.id')
            ->innerJoin(['t6' => UserStudent::tableName()], 't3.id = t6.user_id')
            ->leftJoin(['t4' => StudyTaskProgress::tableName()], 't4.study_task_id = t.study_task_id AND t4.user_id = t3.id')
            ->innerJoin(['t5' => StudyTask::tableName()], 't.study_task_id = t5.id')
            ->where('t.study_task_id = :task AND t.study_group_id = :group', [':task' => $task_id, ':group' => $group_id])
            ->andWhere('t6.status = 1')
            ->orderBy(['t.created_at' => SORT_DESC]);
        $dataProvider = new SqlDataProvider([
            'sql' => $query->createCommand()->getRawSql(),
            'totalCount' => $query->count(),
        ]);
        return $this->render('task_users', [
            'dataProvider' => $dataProvider,
            'assignedModel' => $assignModel,
        ]);
    }
}

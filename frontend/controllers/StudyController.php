<?php

namespace frontend\controllers;

use common\models\StudyTask;
use common\models\StudyTaskAssign;
use common\models\User;
use common\rbac\UserRoles;
use frontend\models\study_task\TaskBeginForm;
use frontend\models\study_task\TaskContinueForm;
use Yii;
use yii\data\SqlDataProvider;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class StudyController extends Controller
{

    public $layout = 'profile';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::ROLE_STUDENT],
                    ],
                ],
            ],
        ];
    }

    public function actionTask(int $id)
    {
        $this->layout = 'main';

        $taskModel = $this->findTaskModel($id);
        $userModel = User::findOne(Yii::$app->user->getId());

        if (!Yii::$app->user->can(UserRoles::ROLE_TEACHER)) {
            try {
                StudyTaskAssign::taskAssignedToUser($taskModel, $userModel);
            } catch (\Exception $ex) {
                Yii::$app->errorHandler->logException($ex);
                throw new NotFoundHttpException('Ошибка доступа к заданию');
            }
        }

        return $this->render('task', [
            'taskModel' => $taskModel,
            'model' => $taskModel->story,
            'userProgress' => $taskModel->getUserProgress(Yii::$app->user->getId()),
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    private function findTaskModel(int $id): ?StudyTask
    {
        if (($model = StudyTask::findTask($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionBegin()
    {
        $form = new TaskBeginForm();
        if ($form->load(Yii::$app->request->post())) {

            try {
                $form->beginTask(Yii::$app->user->getId());
            }
            catch (\Exception $ex) {
                Yii::$app->errorHandler->logException($ex);
                return $this->renderAjax('_error', ['message' => 'Error']);
            }

            $taskModel = $this->findTaskModel($form->task_id);
            return $this->renderAjax('_view', ['taskModel' => $taskModel]);
        }
        return $this->renderAjax('_error', ['message' => 'Unknown error']);
    }

    public function actionContinue()
    {
        $form = new TaskContinueForm();
        if ($form->load(Yii::$app->request->post())) {

            try {
                $form->continueTask();
            }
            catch (\Exception $ex) {
                Yii::$app->errorHandler->logException($ex);
                return $this->renderAjax('_error', ['message' => 'Error']);
            }

            $taskModel = $this->findTaskModel($form->task_id);
            return $this->renderAjax('_view', ['taskModel' => $taskModel]);
        }
        return $this->renderAjax('_error', ['message' => 'Unknown error']);
    }

    public function actionIndex()
    {
        $title = 'Задания';
        $this->getView()->setMetaTags($title,
            $title,
            '',
            $title);

        $query = (new Query())
            ->select(['t3.*', 't2.created_at AS assign_date', 't4.created_at AS begin_date', 't4.status AS task_status'])
            ->from(['t' => '{{%study_group_user%}}'])
            ->innerJoin(['t2' => '{{%study_task_assign%}}'], 't2.study_group_id = t.study_group_id')
            ->innerJoin(['t3' => '{{%study_task%}}'], 't3.id = t2.study_task_id')
            ->leftJoin(['t4' => '{{%study_task_progress%}}'], 't4.study_task_id = t3.id AND t4.user_id = t.user_id')
            ->where('t.user_id = :user', [':user' => Yii::$app->user->getId()])
            ->orderBy(['t2.created_at' => SORT_DESC]);

        $dataProvider = new SqlDataProvider([
            'sql' => $query->createCommand()->getRawSql(),
            'totalCount' => $query->count(),
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }
}
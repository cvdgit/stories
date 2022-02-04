<?php

namespace backend\controllers;

use backend\components\BaseController;
use backend\models\story_list\CreateStoryListForm;
use backend\models\story_list\UpdateStoryListForm;
use common\models\StoryList;
use common\rbac\UserRoles;
use Exception;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;

class StoryListController extends BaseController
{

    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::PERMISSION_MANAGE_STORIES],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $dataProvider = new ActiveDataProvider([
            'query' => StoryList::find(),
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $form = new CreateStoryListForm();
        if ($this->request->isPost && $form->load($this->request->post())) {
            try {
                $id = $form->create();
                Yii::$app->session->setFlash('success', 'Список историй успешно создан');
                return $this->redirect(['update', 'id' => $id]);
            }
            catch (Exception $ex) {
                Yii::$app->session->setFlash('error', $ex->getMessage());
                return $this->refresh();
            }
        }
        return $this->render('create', [
            'model' => $form,
        ]);
    }

    /**
     * @throws Yii\base\InvalidConfigException
     * @throws yii\web\NotFoundHttpException
     */
    public function actionUpdate(int $id)
    {
        /** @var StoryList $model */
        $model = $this->findModel(StoryList::class, $id);
        $form = new UpdateStoryListForm($model);
        if ($this->request->isPost && $form->load($this->request->post())) {
            try {
                $form->update();
                Yii::$app->session->setFlash('success', 'Список историй успешно изменен');
            }
            catch(Exception $ex) {
                Yii::$app->session->setFlash('error', $ex->getMessage());
            }
            return $this->refresh();
        }
        return $this->render('update', [
            'model' => $form,
        ]);
    }

    public function actionDelete(int $id): Response
    {
        /** @var StoryList $model */
        $model = $this->findModel(StoryList::class, $id);
        $model->delete();
        return $this->redirect(['index']);
    }
}

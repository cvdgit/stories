<?php


namespace backend\controllers;


use common\models\News;
use common\rbac\UserRoles;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class NewsController extends Controller
{

    public function actions()
    {
        return [
            'image-upload' => [
                'class' => 'vova07\imperavi\actions\UploadFileAction',
                'url' => 'https://wikids.ru/upload/', // Directory URL address, where files are stored.
                'path' => '@public/upload', // Or absolute path to directory where files are stored.
            ],
        ];
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::PERMISSION_MANAGE_NEWS],
                    ],
                ],
            ],
        ];
    }

    public function actionAdmin($status)
    {
        $query = News::find()->orderBy('created_at DESC');
        $query->andWhere(['status' => $status]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 10],
        ]);
        return $this->render('index',[
            'status' => $status,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new News([
            'status' => News::STATUS_PROPOSED,
        ]);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success');
            return $this->redirect(['admin']);
        }
        else {
            $message = '';
            foreach ($model->getErrors() as $error) {
                $message .= $error[0];
            }
            if (!empty($message)) {
                Yii::$app->session->setFlash('error', $message);
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect($model->getUrl());
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param int $id
     * @return null|News
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = News::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
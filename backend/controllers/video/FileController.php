<?php

namespace backend\controllers\video;

use backend\models\video\CreateFileVideoForm;
use backend\models\video\UpdateFileVideoForm;
use common\models\SlideVideo;
use common\rbac\UserRoles;
use Exception;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class FileController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::PERMISSION_MANAGE_TEST],
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

    public function actionCreate()
    {
        $model = new CreateFileVideoForm();
        if ($model->load(Yii::$app->request->post())) {
            try {
                $id = $model->createVideo();
                Yii::$app->session->addFlash('success', 'Видео успешно добавлено');
                return $this->redirect(['update', 'id' => $id]);
            }
            catch (Exception $ex) {
                Yii::$app->session->addFlash('error', $ex->getMessage());
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate(int $id)
    {
        $videoModel = $this->findModel($id);
        $model = new UpdateFileVideoForm($videoModel);
        if ($model->load(Yii::$app->request->post())) {
            try {
                $model->updateVideo();
                Yii::$app->session->setFlash('success', 'Видео успешно изменен');
            }
            catch (\Exception $ex) {
                Yii::$app->session->setFlash('error', $ex->getMessage());
            }
            return $this->refresh();
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    private function findModel($id)
    {
        if (($model = SlideVideo::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Видео не найдено.');
    }
}
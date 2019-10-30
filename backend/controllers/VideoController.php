<?php


namespace backend\controllers;


use backend\models\video\CreateVideoForm;
use backend\models\video\UpdateVideoForm;
use backend\services\VideoService;
use common\models\SlideVideo;
use common\rbac\UserRoles;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

class VideoController extends Controller
{

    protected $service;

    public function __construct($id, $module, VideoService $service, $config = [])
    {
        $this->service = $service;
        parent::__construct($id, $module, $config);
    }

    public function behaviors()
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
        ];
    }

    public function actionIndex()
    {
        $query = SlideVideo::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ]
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new CreateVideoForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->createVideo();
            return $this->redirect(['index']);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate(int $id)
    {
        $model = new UpdateVideoForm($id);
        $model->loadModel();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->saveVideo();
            return $this->refresh();
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete(int $id)
    {
        $model = SlideVideo::findModel($id);
        $model->delete();
        return $this->redirect(['index']);
    }

    public function actionCheck(int $id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = SlideVideo::findModel($id);
        $isValid = $this->service->checkVideo($model->video_id);
        $model->status = $isValid ? SlideVideo::STATUS_SUCCESS : SlideVideo::STATUS_ERROR;
        $model->save(false, ['status']);
        
        return ['success' => true];
    }

}
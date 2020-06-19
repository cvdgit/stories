<?php


namespace backend\controllers;


use backend\components\story\AbstractBlock;
use backend\components\story\reader\HtmlSlideReader;
use backend\components\story\writer\HTMLWriter;
use backend\models\video\CreateVideoForm;
use backend\models\video\UpdateVideoForm;
use backend\services\VideoService;
use common\models\SlideVideo;
use common\models\Story;
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
        
        return ['success' => $isValid];
    }

    public function actionGetStories(string $video_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = [];
        $models = Story::find()->with('storySlides')->andWhere(['video' => 1])->all();
        foreach ($models as $model) {
            $videoFound = false;
            foreach ($model->storySlides as $slideModel) {
                $reader = new HtmlSlideReader($slideModel->data);
                $slide = $reader->load();
                foreach ($slide->getBlocks() as $block) {
                    if (($block->getType() === AbstractBlock::TYPE_VIDEO) && $block->getVideoId() === $video_id) {
                        $videoFound = true;
                    }
                }
            }
            if ($videoFound) {
                $data[] = [
                    'title' => $model->title,
                    'cover' => $model->getBaseModel()->getCoverRelativePath(),
                ];
            }
        }
        return $data;
    }

}
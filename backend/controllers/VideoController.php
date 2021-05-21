<?php


namespace backend\controllers;


use backend\components\story\AbstractBlock;
use backend\components\story\reader\HtmlSlideReader;
use backend\components\story\writer\HTMLWriter;
use backend\models\SlideVideoSearch;
use backend\models\video\CreateVideoForm;
use backend\models\video\UpdateVideoForm;
use backend\models\video\VideoSource;
use backend\services\VideoService;
use common\models\SlideVideo;
use common\models\Story;
use common\rbac\UserRoles;
use Exception;
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

    public function actionIndex(int $source = VideoSource::YOUTUBE)
    {
        $model = new SlideVideoSearch();
        $params = array_merge([], Yii::$app->request->queryParams);
        $params['SlideVideoSearch']['source'] = $source;
        $dataProvider = $model->search($params);
        return $this->render('index', [
            'searchModel' => $model,
            'dataProvider' => $dataProvider,
            'source' => $source,
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
            try {
                $id = $model->saveVideo();
                Yii::$app->session->addFlash('success', 'Видео успешно изменено');
                return $this->redirect(['update', 'id' => $id]);
            }
            catch (Exception $ex) {
                Yii::$app->session->addFlash('error', $ex->getMessage());
            }
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
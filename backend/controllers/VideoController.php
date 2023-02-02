<?php

namespace backend\controllers;

use backend\actions\ReplaceVideo\ReplaceVideoAction;
use backend\components\story\AbstractBlock;
use backend\components\story\reader\HtmlSlideReader;
use backend\models\SlideVideoSearch;
use backend\models\video\CreateVideoForm;
use backend\models\video\UpdateVideoForm;
use backend\models\video\VideoSource;
use backend\services\VideoService;
use common\models\SlideVideo;
use common\models\Story;
use common\rbac\UserRoles;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class VideoController extends Controller
{
    private $service;

    public function __construct($id, $module, VideoService $service, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
    }

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
        ];
    }

    public function actions(): array
    {
        return [
            'replace' => ReplaceVideoAction::class,
        ];
    }

    public function actionIndex(int $source = VideoSource::YOUTUBE): string
    {
        $model = new SlideVideoSearch();
        $params = array_merge([], \Yii::$app->request->queryParams);
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
        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
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
        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            try {
                $id = $model->saveVideo();
                \Yii::$app->session->addFlash('success', 'Видео успешно изменено');
                return $this->redirect(['update', 'id' => $id]);
            }
            catch (\Exception $ex) {
                \Yii::$app->session->addFlash('error', $ex->getMessage());
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @throws StaleObjectException
     * @throws NotFoundHttpException
     */
    public function actionDelete(int $id): Response
    {
        $model = SlideVideo::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException('Видео не найдено');
        }
        $model->delete();
        return $this->redirect(['index']);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionCheck(int $id): array
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $model = SlideVideo::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException('Видео не найдено');
        }

        $isValid = $this->service->checkVideo($model->video_id);
        $model->status = $isValid ? SlideVideo::STATUS_SUCCESS : SlideVideo::STATUS_ERROR;
        $model->save(false, ['status']);

        return ['success' => $isValid];
    }

    public function actionGetStories(string $video_id): array
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
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

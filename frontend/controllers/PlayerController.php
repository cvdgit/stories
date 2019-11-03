<?php


namespace frontend\controllers;


use common\helpers\Url;
use common\models\Story;
use common\models\StoryAudioTrack;
use common\models\StorySlide;
use common\services\StoryAudioService;
use Exception;
use frontend\models\SlideAudio;
use frontend\models\StoryTrackModel;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;

class PlayerController extends Controller
{

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['moderator'],
                    ],
                ],
            ],
        ];
    }

    protected $audioService;

    public function __construct($id, $module, StoryAudioService $audioService, $config = [])
    {
        $this->audioService = $audioService;
        parent::__construct($id, $module, $config);
    }

    public function actionSetSlideAudio()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new SlideAudio();
        $result = ['success' => false, 'message' => ''];
        if ($model->load(Yii::$app->request->post())) {
            $model->slide_audio_files = UploadedFile::getInstances($model, 'slide_audio_files');
            try {
                $result['success'] = $model->upload();
            }
            catch (Exception $ex) {
                $result['message'] = $ex->getMessage();
            }
            if ($model->hasErrors()) {
                die(print_r($model->errors));
            }
            if ($result['success']) {
                $this->audioService->setSlideAudio($model);
            }
        }
        return $result;
    }

    public function actionCreateAudioTrack(int $story_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = Story::findModel($story_id);
        $track = StoryTrackModel::createTrack('Пользовательская', $model->id, Yii::$app->user->id, StoryAudioTrack::TYPE_USER, 0);
        return ['success' => true, 'track' => $track];
    }

    public function actionGetTrack(int $track_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['success' => true, 'track' => StoryAudioTrack::findModel($track_id)];
    }

    public function actionGetSlide(int $story_id, int $slide_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = StorySlide::findSlide($slide_id);
        $html = $model->data;
        return ['html' => $html];
    }

    public function actionSeeAlsoStories(int $story_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = Story::findModel($story_id);
        $categoryIDs = array_map(function($category) {
            return $category->id;
        }, $model->categories);

        $stories = Story::followingStories($categoryIDs);
        $content = '';
        foreach ($stories as $story) {
            //$storyContent = Html::tag('div', '<span></span>', ['class' => 'story-item-image-overlay']);
            //$storyContent = Html::a('<div class="story-item-image">' . Html::img($story->getBaseModel()->getCoverRelativePath()) . '</div>', ['story/view', 'alias' => $story->alias]);
            //$storyContentWrapper = Html::tag('div', $storyContent, ['class' => 'story-item', 'style' => 'margin-bottom: 20px']);
            //$content .= Html::tag('div', $storyContentWrapper, ['class' => 'col-lg-3 col-md-4 col-sm-6']);

            $content .= $this->renderPartial('_story', ['model' => $story]);
        }
        $html = Html::tag('div', $content, ['class' => 'row flex-row']);

        return ['html' => '<div class="sl-block" data-block-id="9fdcc8e4ed51ca6840da" data-block-type="html" style="width: 1254px;height: 700px;left: 14px;top: 20px;"><div class="sl-block-content" style="z-index: 10">' . $html . '</div></div>'];
    }

}
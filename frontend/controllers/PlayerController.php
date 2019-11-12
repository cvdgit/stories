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
use yii\db\Query;
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

        $stories = [];

        $viewedStory = (new Query())
            ->select('story_id')
            ->from('{{%user_story_history}}')
            ->where('user_id = :user', [':user' => Yii::$app->user->id])
            ->andWhere(['>', 'percent', '80'])
            ->all();
        $viewedStoryIDs = array_map(function ($row) {
            return $row['story_id'];
        }, $viewedStory);
        $viewedStoryIDs[] = $model->id;

        $playlists = $model->playlists;
        if ($playlists !== null) {
            $playlistID = $playlists[0]->id;
            $stories = (new Query())
                ->select('{{%story}}.*')
                ->from('{{%playlist}}')
                ->where('{{%playlist}}.id = :id', [':id' => $playlistID])
                ->innerJoin('{{%story_playlist}}', '{{%playlist}}.id = {{%story_playlist}}.playlist_id')
                ->innerJoin('{{%story}}', '{{%story_playlist}}.story_id = {{%story}}.id')
                ->andWhere('{{%story}}.status = :status', [':status' => Story::STATUS_PUBLISHED])
                ->andWhere(['not in', '{{%story}}.id', $viewedStoryIDs])
                ->orderBy(['-{{%story_playlist}}.order' => SORT_DESC, '{{%story_playlist}}.created_at' => SORT_ASC])
                ->limit(8)
                ->all();
        }

        if (count($stories) === 0) {

            $categoryIDs = array_map(function ($category) {
                return $category->id;
            }, $model->categories);

            $stories = (new Query())
                ->select('{{%story}}.*')
                ->from('{{%category}}')
                ->innerJoin('{{%story_category}}', '{{%story_category}}.category_id = {{%category}}.id')
                ->innerJoin('{{%story}}', '{{%story_category}}.story_id = {{%story}}.id')
                ->where(['in', '{{%category}}.id', $categoryIDs])
                ->andWhere('{{%story}}.status = :status', [':status' => Story::STATUS_PUBLISHED])
                ->andWhere(['not in', '{{%story}}.id', $viewedStoryIDs])
                ->orderBy(['{{%story}}.episode' => SORT_ASC, '{{%story}}.created_at' => SORT_DESC])
                ->limit(8)
                ->all();
        }

        if (count($stories) === 0) {
            $stories = (new Query())
                ->select('*')
                ->from('{{%story}}')
                ->where('status = :status', [':status' => Story::STATUS_PUBLISHED])
                ->andWhere(['not in', 'id', [$model->id]])
                ->orderBy('rand()')
                ->limit(8)
                ->all();
        }

        $content = '';
        foreach ($stories as $story) {
            $content .= $this->renderPartial('_story', ['model' => $story]);
        }
        $html = Html::tag('div', $content, ['class' => 'row flex-row']);

        return ['html' => '<div class="sl-block" data-block-id="9fdcc8e4ed51ca6840da" data-block-type="html" style="width: 1294px;height: 727px;left: -7px;top: -4px;"><div class="sl-block-content" style="z-index: 10">' . $html . '<div class="autoplay-overlay"></div></div></div>'];
    }

}
<?php
use common\widgets\Reveal\Plugins\SlideLinks;
use common\widgets\Reveal\Plugins\Video;
use frontend\assets\PlyrAsset;
use frontend\assets\RecorderAsset;
use frontend\widgets\FrontendRevealWidget;
use yii\helpers\Json;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model common\models\Story */
/* @var $userCanViewStory bool */
/* @var $saveStat bool */

$config = [
    'setSlideAudioAction' => Url::to(['player/set-slide-audio']),
    'loadStoryAction' => Url::to(['story/init-story-player', 'id' => $model->id]),
    'story' => [
        'video' => $model->video,
    ],
    'storyID' => $model->id,
];
$configJSON = Json::htmlEncode($config);
$js = <<< JS
    WikidsPlayer.initialize($configJSON);
JS;
$this->registerJs($js);

/** @var $playlistID int? */
$plugins = [
    [
        'class' => \common\widgets\Reveal\Plugins\CustomControls::class,
        'buttons' => [
            new \common\widgets\RevealButtons\RecorderButton(),
            new \common\widgets\RevealButtons\LeftButton(),
            new \common\widgets\RevealButtons\RightButton(),
            new \common\widgets\RevealButtons\FullscreenButton(),
            new \common\widgets\RevealButtons\FeedbackButton(),
        ],
        'rightButtons' => []
    ],
    ['class' => \common\widgets\Reveal\Plugins\Feedback::class, 'storyID' => $model->id],
    ['class' => \common\widgets\Reveal\Plugins\Transition::class, 'storyID' => $model->id],
    ['class' => \common\widgets\Reveal\Plugins\Test::class, 'storyID' => $model->id],
    ['class' => \common\widgets\Reveal\Plugins\Background::class],
    ['class' => SlideLinks::class, 'storyID' => $model->id, 'links' => $model->slideBlocksData()],
    ['class' => Video::class, 'showControls' => \common\rbac\UserRoles::isModerator(Yii::$app->user->id)],
    ['class' => \common\widgets\Reveal\Plugins\Actions::class],
    ['class' => \common\widgets\Reveal\Plugins\SeeAlso::class, 'storyID' => $model->id, 'isPlaylist' => ($playlistID !== null)],
    ['class' => \common\widgets\Reveal\Plugins\Recorder::class, 'story' => $model],
    ['class' => \common\widgets\Reveal\Plugins\SlideState::class, 'storyID' => $model->id],
    ['class' => \common\widgets\Reveal\Plugins\SlideLinksView::class],
];

if ($saveStat) {
    $plugins[] = ['class' => \common\widgets\Reveal\Plugins\Statistics::class, 'storyID' => $model->id];
}

/** @var $audioTrackPath string */
if (($model->isAudioStory() || $model->isUserAudioStory(Yii::$app->user->id)) && $audioTrackPath !== '') {
    $plugins[] = [
        'class' => \common\widgets\Reveal\Plugins\Audio::class,
        'storyID' => $model->id,
        'prefix' => $audioTrackPath . DIRECTORY_SEPARATOR,
        'autoplay' => true,
    ];
}

echo FrontendRevealWidget::widget([
    'storyId' => $model->id,
    'data' => $model->slidesData(),
    'canViewStory' => $userCanViewStory,
    'assets' => [
        PlyrAsset::class,
        RecorderAsset::class,
    ],
    'plugins' => $plugins,
]);

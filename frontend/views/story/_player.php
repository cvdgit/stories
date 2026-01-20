<?php

use backend\assets\MainAsset;
use common\models\Story;
use common\rbac\UserRoles;
use common\widgets\Reveal\Plugins\Retelling;
use common\widgets\Reveal\Plugins\SlideLinks;
use common\widgets\Reveal\Plugins\Video;
use frontend\assets\PlyrAsset;
use frontend\assets\RecorderAsset;
use frontend\widgets\FrontendRevealWidget;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 * @var Story $model
 * @var bool $userCanViewStory
 * @var bool $saveStat
 * @var array $completedRetelling
 * @var array $contentMentalMaps
 * @var int $userId
 */

MainAsset::register($this);

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

$buttons = [
    //new \common\widgets\RevealButtons\RecorderButton(),
    new \common\widgets\RevealButtons\LeftButton(),
    new \common\widgets\RevealButtons\RightButton(),
    new \common\widgets\RevealButtons\FullscreenButton(),
    new \common\widgets\RevealButtons\FeedbackButton(),

];

/*$retellingUsers = Yii::$app->params['retelling.access.users'] ?? [];
$canRetellingAccess = Yii::$app->user->can(UserRoles::ROLE_MODERATOR) || in_array(Yii::$app->user->getId(), $retellingUsers, true);

if ($canRetellingAccess) {
    $buttons[] = new RetellingButton();
    $buttons[] = new RetellingAnswersButton();
}*/

$plugins = [
    [
        'class' => \common\widgets\Reveal\Plugins\CustomControls::class,
        'buttons' => $buttons,
        'rightButtons' => []
    ],
    ['class' => \common\widgets\Reveal\Plugins\Feedback::class, 'storyID' => $model->id],
    ['class' => \common\widgets\Reveal\Plugins\Transition::class, 'storyID' => $model->id],
    ['class' => \common\widgets\Reveal\Plugins\Test::class, 'storyID' => $model->id],
    ['class' => \common\widgets\Reveal\Plugins\Background::class],
    ['class' => SlideLinks::class, 'storyID' => $model->id, 'links' => $model->slideBlocksData()],
    ['class' => Video::class, 'showControls' => UserRoles::isModerator(Yii::$app->user->id)],
    ['class' => \common\widgets\Reveal\Plugins\Actions::class],
    ['class' => \common\widgets\Reveal\Plugins\SeeAlso::class, 'storyID' => $model->id, 'isPlaylist' => ($playlistID !== null)],
    //['class' => \common\widgets\Reveal\Plugins\Recorder::class, 'story' => $model],
    ['class' => \common\widgets\Reveal\Plugins\SlideLinksView::class],
    ['class' => \common\widgets\Reveal\Plugins\MentalMap::class, 'storyId' => $model->id],

    ['class' => Retelling::class, 'storyId' => $model->id],
    ['class' => \common\widgets\Reveal\Plugins\ContentMentalMap::class, 'storyId' => $model->id, 'mentalMaps' => $contentMentalMaps],
    [
        'class' => \common\widgets\Reveal\Plugins\TableOfContents::class,
        'storyId' => $model->id,
        'userId' => $userId,
    ],
];

if ($model->isScreenRecorder()) {
    $plugins[] = [
        'class' => \common\widgets\Reveal\Plugins\ScreenRecorderPlugin::class,
        'storyId' => $model->id,
        'userId' => $userId,
    ];
}

if (Yii::$app->request->get('from_game') === null) {
    $plugins[] = ['class' => \common\widgets\Reveal\Plugins\SlideState::class, 'storyID' => $model->id];
}

//if ($canRetellingAccess) {
    //$plugins[] = ["class" => Retelling::class, 'storyId' => $model->id, 'completed' => $completedRetelling];
//}

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
        \common\assets\panzoom\PanzoomAsset::class,
        \common\assets\panzoom\PanzoomOldAsset::class,
    ],
    'plugins' => $plugins,
]);

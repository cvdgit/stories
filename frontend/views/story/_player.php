<?php

use common\widgets\RevealButtons\BackgroundButton;
use common\widgets\RevealWidget;

/* @var $this yii\web\View */
/* @var $model common\models\Story */
/* @var $userCanViewStory bool */

$plugins = [
    [
        'class' => \common\widgets\Reveal\Plugins\CustomControls::class,
        'buttons' => [
            new \common\widgets\RevealButtons\LeftButton(),
            new \common\widgets\RevealButtons\RightButton(),
            new \common\widgets\RevealButtons\FullscreenButton(),
            new BackgroundButton(),
            new \common\widgets\RevealButtons\FeedbackButton(),
        ],
        'rightButtons' => []
    ],
    ['class' => \common\widgets\Reveal\Plugins\Feedback::class, 'storyID' => $model->id],
    ['class' => \common\widgets\Reveal\Plugins\Statistics::class, 'storyID' => $model->id],
    ['class' => \common\widgets\Reveal\Plugins\Transition::class, 'storyID' => $model->id],
    ['class' => \common\widgets\Reveal\Plugins\Background::class],
];

if ($model->isAudioStory()) {
    $plugins[] = ['class' => \common\widgets\Reveal\Plugins\Audio::class, 'storyID' => $model->id];
}

echo RevealWidget::widget([
    'storyId' => $model->id,
    'data' => $model->slidesData(),
    'canViewStory' => $userCanViewStory,
    'assets' => [
        \frontend\assets\RevealAsset::class,
        \frontend\assets\WikidsRevealAsset::class,
    ],
    'plugins' => $plugins,
]);

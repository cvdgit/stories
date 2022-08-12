<?php

namespace frontend\widgets;

use common\models\Story;
use common\models\StudyTask;
use common\widgets\Reveal\Plugins\SlideLinks;
use common\widgets\Reveal\Plugins\Video;
use common\widgets\RevealWidget;
use frontend\assets\MaphilightAsset;
use frontend\assets\PlyrAsset;
use frontend\assets\RecorderAsset;
use frontend\assets\SortableJsAsset;
use frontend\assets\WikidsRevealAsset;

class EduRevealWidget extends RevealWidget
{

    /** @var Story */
    public $model;

    protected $defaultAssets = [
        WikidsRevealAsset::class,
    ];

    public function init()
    {

        $this->storyId = $this->model->id;
        $this->data = $this->model->slidesData();
        $this->canViewStory = true;
/*
        $this->assets = [
            PlyrAsset::class,
            RecorderAsset::class,
            SortableJsAsset::class,
            MaphilightAsset::class,
        ];
        */
        $this->plugins = [
            [
                'class' => \common\widgets\Reveal\Plugins\CustomControls::class,
                'buttons' => [
                    new \common\widgets\RevealButtons\LeftButton(),
                    new \common\widgets\RevealButtons\RightButton(),
                    new \common\widgets\RevealButtons\FullscreenButton(),
                ],
                'rightButtons' => [],
            ],
            //['class' => \common\widgets\Reveal\Plugins\Transition::class, 'storyID' => $this->storyId],
            //['class' => \common\widgets\Reveal\Plugins\Statistics::class, 'storyID' => $this->storyId],
            //['class' => \common\widgets\Reveal\Plugins\Test::class, 'storyID' => $this->storyId],
            //['class' => SlideLinks::class, 'storyID' => $this->storyId, 'links' => $this->model->slideBlocksData()],
            //['class' => Video::class, 'showControls' => false],
            //['class' => \common\widgets\Reveal\Plugins\Actions::class],
            //['class' => \common\widgets\Reveal\Plugins\SlideState::class, 'storyID' => $this->model->id],
        ];


        $this->options = [
            'backgroundTransition' => 'none',
            'history' => false,
            'hash' => false,
        ];
        parent::init();
    }
}

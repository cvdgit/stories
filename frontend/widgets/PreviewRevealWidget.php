<?php

namespace frontend\widgets;

use common\models\Story;
use common\widgets\Reveal\Plugins\SlideLinks;
use common\widgets\Reveal\Plugins\Video;
use common\widgets\RevealWidget;
use frontend\assets\PlyrAsset;
use frontend\assets\RecorderAsset;
use frontend\assets\RevealAsset;
use frontend\assets\SortableJsAsset;
use frontend\assets\WikidsRevealAsset;
use Yii;

class PreviewRevealWidget extends RevealWidget
{

    /** @var Story */
    public $model;

    protected $defaultAssets = [
        RevealAsset::class,
        WikidsRevealAsset::class,
    ];

    public function init()
    {
        $this->storyId = $this->model->id;
        $this->data = $this->model->slidesData();
        $this->canViewStory = true;
        $this->assets = [
            PlyrAsset::class,
            RecorderAsset::class,
            SortableJsAsset::class,
        ];
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
            ['class' => \common\widgets\Reveal\Plugins\Transition::class, 'storyID' => $this->storyId],
            ['class' => \common\widgets\Reveal\Plugins\Test::class, 'storyID' => $this->storyId],
            ['class' => SlideLinks::class, 'storyID' => $this->storyId, 'links' => $this->model->slideBlocksData()],
            ['class' => Video::class, 'showControls' => false],
            ['class' => \common\widgets\Reveal\Plugins\Actions::class],
            //['class' => \common\widgets\Reveal\Plugins\SeeAlso::class, 'storyID' => $model->id, 'isPlaylist' => ($playlistID !== null)],
        ];
        $this->options = [
            'backgroundTransition' => 'none',
        ];
        parent::init();
    }

}
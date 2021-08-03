<?php

namespace frontend\widgets;

use common\models\Story;
use common\widgets\RevealWidget;
use frontend\assets\WikidsRevealAsset;

class HintsRevealWidget extends RevealWidget
{

    /** @var Story */
    public $model;

    protected $defaultAssets = [
        WikidsRevealAsset::class,
    ];

    public function init()
    {
        $this->storyId = $this->model->id;
        $this->canViewStory = true;
        $this->assets = [];
        $this->plugins = [
            [
                'class' => \common\widgets\Reveal\Plugins\CustomControls::class,
                'buttons' => [
                    new \common\widgets\RevealButtons\LeftButton(),
                    new \common\widgets\RevealButtons\RightButton(),
                ],
                'rightButtons' => [],
            ],
        ];
        $this->options = [
            'backgroundTransition' => 'none',
        ];
        parent::init();
    }
}

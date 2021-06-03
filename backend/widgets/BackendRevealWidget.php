<?php

namespace backend\widgets;

use backend\assets\RevealAsset;
use backend\assets\WikidsRevealAsset;
use common\widgets\Reveal\Plugins\Video;
use common\widgets\RevealWidget;

class BackendRevealWidget extends RevealWidget
{

    protected $defaultAssets = [
        RevealAsset::class,
        WikidsRevealAsset::class,
    ];

    public function init()
    {
        $this->initializeReveal = true;
        $this->canViewStory = true;
        $this->options = [
            'history' => false,
            'hash' => false,
            'progress' => false,
            'slideNumber' => false,
            'maxScale' => 1,
        ];
        $this->assets = [
            RevealAsset::class,
            WikidsRevealAsset::class,
        ];
        $this->plugins = [
            ['class' => Video::class, 'showControls' => true],
        ];
        parent::init();
    }

}
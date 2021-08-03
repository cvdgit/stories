<?php

namespace backend\widgets;

use backend\assets\WikidsRevealAsset;
use common\widgets\Reveal\Plugins\Video;
use common\widgets\RevealWidget;

class BackendRevealWidget extends RevealWidget
{

    protected $defaultAssets = [
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
        $this->plugins = [
            ['class' => Video::class, 'showControls' => true],
        ];
        parent::init();
    }

}
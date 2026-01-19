<?php

declare(strict_types=1);

namespace backend\widgets;

use backend\assets\WikidsRevealAsset;
use common\widgets\Reveal\Plugins\TableOfContents;
use common\widgets\Reveal\Plugins\Video;
use common\widgets\RevealWidget;

class BackendRevealWidget extends RevealWidget
{
    protected $defaultAssets = [
        WikidsRevealAsset::class,
    ];

    public function init(): void
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
            ['class' => TableOfContents::class],
        ];
        parent::init();
    }
}

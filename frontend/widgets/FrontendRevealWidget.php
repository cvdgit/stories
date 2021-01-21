<?php

namespace frontend\widgets;

use common\widgets\RevealWidget;

class FrontendRevealWidget extends RevealWidget
{

    protected $defaultAssets = [
        \frontend\assets\RevealAsset::class,
        \frontend\assets\WikidsRevealAsset::class,
    ];

}
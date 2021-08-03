<?php

namespace frontend\widgets;

use common\widgets\RevealWidget;
use frontend\assets\WikidsRevealAsset;

class FrontendRevealWidget extends RevealWidget
{

    protected $defaultAssets = [
        WikidsRevealAsset::class,
    ];
}

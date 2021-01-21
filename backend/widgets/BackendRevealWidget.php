<?php

namespace backend\widgets;

use common\widgets\RevealWidget;

class BackendRevealWidget extends RevealWidget
{

    protected $defaultAssets = [
        \backend\assets\RevealAsset::class,
        \backend\assets\WikidsRevealAsset::class,
    ];

}
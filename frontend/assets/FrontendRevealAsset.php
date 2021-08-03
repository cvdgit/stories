<?php

namespace frontend\assets;

use common\assets\RevealAsset;

class FrontendRevealAsset extends RevealAsset
{
    public $depends = [
        AppAsset::class,
    ];
}

<?php

namespace backend\assets;

use yii\web\AssetBundle;

class SortableJsAsset extends AssetBundle
{
    public $sourcePath = '@bower/sortablejs';
    public $js = ['Sortable.js'];
}

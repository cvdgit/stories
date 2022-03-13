<?php

namespace common\widgets\Reveal;

use Yii;

class Dependency
{

    public $src;
    public $condition;

    public function __construct($src, $condition = null)
    {
        $this->src = $this->appendTimestamp($src);
        $this->condition = $condition;
    }

    protected function appendTimestamp($src)
    {
        $basePath = Yii::getAlias('@public');
        if (($timestamp = @filemtime("$basePath/$src")) > 0) {
            return "$src?v=$timestamp";
        }
        return $src;
    }

}
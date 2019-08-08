<?php


namespace common\widgets\Reveal;


use Yii;

class Dependency
{
    public $src;

    public function __construct($src)
    {
        $this->src = $this->appendTimestamp($src);
    }

    protected function appendTimestamp($src)
    {
        $basePath = Yii::getAlias('@webroot');
        if (($timestamp = @filemtime("$basePath/$src")) > 0) {
            return "$src?v=$timestamp";
        }
        return $src;
    }

}
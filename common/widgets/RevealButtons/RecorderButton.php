<?php


namespace common\widgets\RevealButtons;


use yii\web\JsExpression;

class RecorderButton extends Button
{
    public function __construct()
    {
        $this->icon = 'glyphicon glyphicon-music';
        $this->className = 'custom-recorder';
        $this->title = 'Recorder';
        $this->action = new JsExpression('function() { if (window["WikidsRecorder"]) WikidsRecorder.showRecorder(); }');
    }
}
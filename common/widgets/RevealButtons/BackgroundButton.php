<?php


namespace common\widgets\RevealButtons;


use yii\web\JsExpression;

class BackgroundButton extends Button
{

    public function __construct()
    {
        $this->icon = 'glyphicon glyphicon-adjust';
        $this->className = 'background-icon';
        $this->title = 'Светлая тема';
        $js = 'function() { StoryBackground.switchBackground(); }';
        $this->action = new JsExpression($js);
    }

}

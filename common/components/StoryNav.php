<?php

namespace common\components;

use yii\bootstrap\Nav as BaseNav;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;

class StoryNav extends BaseNav {

    /**
     * Initializes the widget.
     */
    public function init()
    {
        parent::init();
        Html::removeCssClass($this->options, ['widget' => 'nav']);
    }

}
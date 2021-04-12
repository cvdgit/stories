<?php

namespace backend\helpers;

use Yii;

class StoryHelper
{

    public static function createStoryViewUrl(string $alias)
    {
        return Yii::$app->urlManagerFrontend->createAbsoluteUrl(['story/view', 'alias' => $alias]);
    }

}
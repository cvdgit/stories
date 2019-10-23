<?php


namespace common\helpers;


use common\models\Story;
use yii\helpers\ArrayHelper;

class StoryHelper
{

    public static function getStoryArray(): array
    {
        return ArrayHelper::map(Story::find()->orderBy(['title' => SORT_ASC])->all(), 'id', 'title');
    }

}
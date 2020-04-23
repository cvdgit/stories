<?php

namespace common\helpers;

use common\models\Story;
use Yii;
use yii\helpers\ArrayHelper;

class StoryHelper
{

    public static function getStoryArray(): array
    {
        return ArrayHelper::map(Story::find()->orderBy(['title' => SORT_ASC])->all(), 'id', 'title');
    }

    public static function getImagesPath($model, $web = false)
    {
        $folder = $model->id;
        if ($model->story_file !== null) {
            $folder = $model->story_file;
        }
        return ($web ? '' : Yii::getAlias('@public')) . '/slides/' . $folder;
    }

}
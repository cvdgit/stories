<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\StoryList;
use yii\rest\ActiveController;

class StoryListController extends ActiveController
{

    public $modelClass = StoryList::class;

}
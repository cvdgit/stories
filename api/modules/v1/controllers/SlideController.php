<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\StorySlide;
use yii\rest\ActiveController;

class SlideController extends ActiveController
{

    public $modelClass = StorySlide::class;


}
<?php


namespace api\modules\v1\controllers;


use api\modules\v1\models\Story;
use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;

class StoryController extends ActiveController
{

    public $modelClass = 'api\modules\v1\models\Story';

}
<?php


namespace api\modules\v1\controllers;

use api\modules\v1\models\Story;
use yii\data\ActiveDataProvider;
use yii\rest\Controller;

class StoryController extends Controller
{

    public function actionIndex()
    {
        return new ActiveDataProvider([
            'query' => Story::find(),
        ]);
    }

}
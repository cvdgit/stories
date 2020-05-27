<?php


namespace api\modules\v1\controllers;


use yii\rest\Controller;

class StoryController extends Controller
{

    public function actionIndex()
    {
        return ['hello' => 'world'];
    }

}
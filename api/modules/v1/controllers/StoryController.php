<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\Story;
use api\modules\v1\models\StorySearch;
use Yii;
use yii\rest\ActiveController;

class StoryController extends ActiveController
{

    public $modelClass = Story::class;

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        return $actions;
    }

    public function prepareDataProvider()
    {
        $search = new StorySearch();
        return $search->search(Yii::$app->request->getQueryParams());
    }

}
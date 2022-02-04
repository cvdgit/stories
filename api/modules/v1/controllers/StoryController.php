<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\Story;
use api\modules\v1\models\StorySearch;
use Yii;
use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;

class StoryController extends ActiveController
{

    /**
     * @inheritdoc
     */
    public $modelClass = Story::class;

    /**
     * @inheritdoc
     */
    public function actions(): array
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        return $actions;
    }

    public function prepareDataProvider(): ActiveDataProvider
    {
        return (new StorySearch())->search(Yii::$app->request->getQueryParams());
    }
}

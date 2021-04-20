<?php

namespace console\controllers;

use common\models\Story;
use console\services\PublicationService;
use yii\console\Controller;

class PublicationController extends Controller
{

    private $service;

    public function __construct($id, $module, PublicationService $service, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
    }

    public function actionStories()
    {
        $models = Story::getToPublishStories();
        try {
            $this->service->sendEmail($models);
        }
        catch (\Exception $ex) {
            \Yii::$app->errorHandler->logException($ex);
        }
        foreach ($models as $model) {
            /** @var $model Story */
            $model->publishStory();
        }
    }
}
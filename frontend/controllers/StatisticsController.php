<?php

namespace frontend\controllers;

use common\rbac\UserRoles;
use common\services\story\CountersService;
use Yii;
use common\models\Story;
use common\models\StoryStatistics;
use yii\web\Controller;
use yii\web\Response;

class StatisticsController extends Controller
{

    protected $countersService;

    public function __construct($id, $module, CountersService $countersService, $config = [])
    {
        $this->countersService = $countersService;
        parent::__construct($id, $module, $config);
    }

    public function actionWrite($id)
	{
        Yii::$app->response->format = Response::FORMAT_JSON;
        $storeStatistics = $this->countersService->needUpdateCounters();
        if ($storeStatistics) {
            $story = Story::findModel($id);
            $post = Yii::$app->request->post();
            $model = new StoryStatistics();
            $model->story_id = $story->id;
            $model->slide_number = $post['slide_number'];
            $model->begin_time = $post['begin_time'];
            $model->end_time = $post['end_time'];
            $model->chars = $post['chars'];
            $model->session = $post['session'];
            $model->save();
            return ['success' => true];
        }
        return ['success' => false];
	}

}

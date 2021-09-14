<?php

namespace frontend\controllers;

use common\models\study_task\StudyTaskProgressStatus;
use common\services\story\CountersService;
use frontend\models\SlideStatForm;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class StatisticsController extends Controller
{

    private $countersService;

    public function __construct($id, $module, CountersService $countersService, $config = [])
    {
        $this->countersService = $countersService;
        parent::__construct($id, $module, $config);
    }

    public function actionWrite()
	{
        Yii::$app->response->format = Response::FORMAT_JSON;
        $storeStatistics = $this->countersService->needUpdateCounters();
        if ($storeStatistics) {

            $model = new SlideStatForm();
            if ($model->load(Yii::$app->request->post(), '')) {

                $model->saveStat(Yii::$app->user->getId());

                if (!Yii::$app->user->isGuest) {
                    if ($model->needUpdateStudyTaskStatus()) {
                        $status = StudyTaskProgressStatus::PROGRESS;
                        if ($model->isLastSlide()) {
                            $status = StudyTaskProgressStatus::DONE;
                        }
                        StudyTaskProgressStatus::setStatus($model->study_task_id, Yii::$app->user->getId(), $status);
                    }
                    else {
                        $this->countersService->calculateStoryHistoryPercentage(Yii::$app->user->id, $model->story_id);
                    }
                }
                return ['success' => true];
            }
        }
        return ['success' => false];
	}
}

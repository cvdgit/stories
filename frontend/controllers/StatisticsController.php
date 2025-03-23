<?php

namespace frontend\controllers;

use common\models\study_task\StudyTaskProgressStatus;
use common\services\story\CountersService;
use Exception;
use frontend\models\SlideStatForm;
use frontend\models\StoryStudentStatForm;
use frontend\services\StoryStatService;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class StatisticsController extends Controller
{
    private $countersService;
    private $storyStatService;

    public function __construct($id, $module, CountersService $countersService, StoryStatService $storyStatService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->countersService = $countersService;
        $this->storyStatService = $storyStatService;
    }

    public function actionWrite()
	{
        Yii::$app->response->format = Response::FORMAT_JSON;
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
        return ['success' => false];
	}

    public function actionWriteEdu(): array
    {
        $this->response->format = Response::FORMAT_JSON;

        $form = new StoryStudentStatForm();
        if ($this->request->isPost && $form->load($this->request->post(), '')) {

            try {
                $stat = $this->storyStatService->saveStudentStat($form);
                return ['success' => true, 'stat' => $stat];
            }
            catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                return ['success' => false, 'message' => $exception->getMessage()];
            }
        }

        return ['success' => false];
    }
}

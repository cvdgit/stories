<?php

declare(strict_types=1);

namespace frontend\controllers;

use common\models\study_task\StudyTaskProgressStatus;
use common\services\story\CountersService;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use frontend\models\SlideStatForm;
use frontend\models\StoryStudentStatForm;
use frontend\services\StoryStatService;
use modules\edu\RequiredStory\UpdateSessionCommand;
use modules\edu\RequiredStory\UpdateSessionHandler;
use Yii;
use yii\web\Controller;
use yii\web\Request;
use yii\web\Response;
use yii\web\User as WebUser;

class StatisticsController extends Controller
{
    private $countersService;
    private $storyStatService;
    /**
     * @var UpdateSessionHandler
     */
    private $updateSessionHandler;

    public function __construct(
        $id,
        $module,
        CountersService $countersService,
        StoryStatService $storyStatService,
        UpdateSessionHandler $updateSessionHandler,
        $config = []
    ) {
        $this->countersService = $countersService;
        $this->storyStatService = $storyStatService;
        parent::__construct($id, $module, $config);
        $this->updateSessionHandler = $updateSessionHandler;
    }

    public function actionWrite(Request $request, Response $response, WebUser $user): array
    {
        $response->format = Response::FORMAT_JSON;
        $model = new SlideStatForm();
        if ($model->load($request->post(), '')) {
            $model->saveStat($user->getId());
            if (!$user->isGuest) {
                if ($model->needUpdateStudyTaskStatus()) {
                    $status = StudyTaskProgressStatus::PROGRESS;
                    if ($model->isLastSlide()) {
                        $status = StudyTaskProgressStatus::DONE;
                    }
                    StudyTaskProgressStatus::setStatus((int) $model->study_task_id, $user->getId(), $status);
                } else {
                    $this->countersService->calculateStoryHistoryPercentage($user->id, (int) $model->story_id);
                }
            }
            return ['success' => true];
        }
        return ['success' => false];
    }

    public function actionWriteEdu(Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $form = new StoryStudentStatForm();
        if ($request->isPost && $form->load($request->post(), '')) {
            try {
                $stat = $this->storyStatService->saveStudentStat($form);
                $this->updateSessionHandler->handle(
                    new UpdateSessionCommand(
                        (int) $form->student_id,
                        (int) $form->story_id,
                        new DateTimeImmutable('now', new DateTimeZone('Europe/Moscow'))
                    )
                );
                return ['success' => true, 'stat' => $stat];
            } catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                return ['success' => false, 'message' => $exception->getMessage()];
            }
        }
        return ['success' => false];
    }
}

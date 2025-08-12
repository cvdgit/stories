<?php

declare(strict_types=1);

namespace backend\controllers\editor;

use backend\components\story\MentalMapBlockContent;
use backend\MentalMap\MentalMap;
use backend\SlideEditor\CopyMentalMap\CopyForm;
use backend\SlideEditor\CopyMentalMap\CopyMentalMapSlideCommand;
use backend\SlideEditor\CopyMentalMap\CopyMentalMapSlideHandler;
use common\models\StorySlide;
use common\rbac\UserRoles;
use Exception;
use Ramsey\Uuid\Uuid;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;
use yii\web\User as WebUser;

class CopySlideController extends Controller
{
    /**
     * @var CopyMentalMapSlideHandler
     */
    private $copyMentalMapSlideHandler;

    public function __construct($id, $module, CopyMentalMapSlideHandler $copyMentalMapSlideHandler, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->copyMentalMapSlideHandler = $copyMentalMapSlideHandler;
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::PERMISSION_EDITOR_ACCESS],
                    ],
                ],
            ],
        ];
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionMentalMapForm(string $id, int $slide_id): string
    {
        $mentalMap = MentalMap::findOne($id);
        if ($mentalMap === null) {
            throw new NotFoundHttpException('Mental map not found');
        }
        $slide = StorySlide::findOne($slide_id);
        if ($slide === null) {
            throw new NotFoundHttpException('Slide not found');
        }
        $copyModel = new CopyForm([
            'id' => $mentalMap->uuid,
            'name' => 'Копия ' . $mentalMap->name,
            'slideId' => $slide->id,
        ]);
        return $this->renderAjax('_copy_mental_map', [
            'formModel' => $copyModel,
        ]);
    }

    public function actionMentalMap(Request $request, Response $response, WebUser $user, int $lesson_id = null): array
    {
        $response->format = Response::FORMAT_JSON;

        $copyModel = new CopyForm();
        if ($copyModel->load($request->post())) {

            if (!$copyModel->validate()) {
                return ['success' => false, 'errors' => $copyModel->getErrors()];
            }

            $currentSlide = StorySlide::findOne($copyModel->slideId);
            if ($currentSlide === null) {
                return ['success' => false, 'Current slide not found'];
            }

            $mentalMap = MentalMap::findOne($copyModel->id);
            if ($mentalMap === null) {
                return ['success' => false, 'message' => 'MentalMap not found'];
            }

            if ($mentalMap->typeIsQuestions()) {
                return ['success' => false, 'message' => 'Only for mental-map types'];
            }

            $content = MentalMapBlockContent::createFromHtml($currentSlide->data);

            try {
                $newSlideId = $this->copyMentalMapSlideHandler->handle(
                    new CopyMentalMapSlideCommand(
                        $currentSlide->story_id,
                        $currentSlide->id,
                        Uuid::fromString($copyModel->id),
                        $copyModel->name,
                        $user->getId(),
                        $content->isRequired(),
                    )
                );
                return ['success' => true, 'id' => $newSlideId];
            } catch (Exception $e) {
                Yii::$app->errorHandler->logException($e);
                return ["success" => false, "errors" => $e->getMessage()];
            }
        }
        return ['success' => false, 'errors' => ['No data']];
    }
}

<?php

declare(strict_types=1);

namespace backend\controllers\editor;

use backend\actions\SlideImport\ImportHandler;
use backend\actions\SlideImport\SlidesImportCommand;
use backend\components\BaseController;
use backend\SlideEditor\ImportSlidesFromStory\ImportSlidesForm;
use common\models\StorySlide;
use Exception;
use Yii;
use yii\web\Request;
use yii\web\Response;
use yii\web\User as WebUser;

class ImportSlidesController extends BaseController
{
    /**
     * @var ImportHandler
     */
    private $importHandler;

    public function __construct($id, $module, ImportHandler $importHandler, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->importHandler = $importHandler;
    }

    public function actionForm(int $storyId, int $currentSlideId = null): string
    {
        $importForm = new ImportSlidesForm([
            'toStoryId' => $storyId,
            'currentSlideId' => $currentSlideId,
        ]);
        return $this->renderAjax('_form', [
            'formModel' => $importForm,
        ]);
    }

    public function actionImport(Request $request, Response $response, WebUser $user): array
    {
        $response->format = Response::FORMAT_JSON;
        $importForm = new ImportSlidesForm();
        if ($importForm->load($request->post())) {
            if (!$importForm->validate()) {
                return ['success' => false, 'message' => 'Not valid'];
            }

            $slideIds = array_map(static function(StorySlide $slide) {
                return $slide->id;
            }, StorySlide::findStorySlides((int) $importForm->fromStoryId));

            try {
                $this->importHandler->handle(
                    new SlidesImportCommand(
                        (int) $importForm->fromStoryId,
                        (int) $importForm->toStoryId,
                        $user->getId(),
                        $slideIds,
                        $importForm->currentSlideId === '' ? null : (int) $importForm->currentSlideId
                    )
                );
                return ['success' => true];
            } catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                return ['success' => false, 'message' => $exception->getMessage()];
            }

        }
        return ['success' => false, 'message' => 'No data'];
    }
}

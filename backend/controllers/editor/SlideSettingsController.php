<?php

declare(strict_types=1);

namespace backend\controllers\editor;

use backend\components\BaseController;
use backend\SlideEditor\SlideSettings\SaveSlideSettingsCommand;
use backend\SlideEditor\SlideSettings\SaveSlideSettingsHandler;
use backend\SlideEditor\SlideSettings\SlideSettingsForm;
use backend\SlideEditor\SlideSettings\SlideSettingsPayload;
use common\models\StorySlide;
use common\rbac\UserRoles;
use Exception;
use Yii;
use yii\base\InvalidConfigException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;

class SlideSettingsController extends BaseController
{
    /**
     * @var SaveSlideSettingsHandler
     */
    private $saveHandler;

    public function __construct($id, $module, SaveSlideSettingsHandler $saveHandler, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->saveHandler = $saveHandler;
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::PERMISSION_MANAGE_STORIES],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'save' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function actionForm(int $id): string
    {
        $slideModel = $this->findModel(StorySlide::class, $id);
        $form = new SlideSettingsForm();
        $payload = SlideSettingsPayload::fromPayload($slideModel->settings ?? []);
        $form->load($payload->asArray(), '');
        return $this->renderAjax('_form', [
            'slideId' => $slideModel->id,
            'formModel' => $form,
        ]);
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function actionSave(int $id, Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $form = new SlideSettingsForm();
        if ($form->load($request->post())) {
            if (!$form->validate()) {
                return ['success' => false, 'message' => 'Not valid'];
            }
            try {
                $this->saveHandler->handle(
                    new SaveSlideSettingsCommand(
                        $id,
                        new SlideSettingsPayload(
                            $form->isSpeakSlideText()
                        )
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

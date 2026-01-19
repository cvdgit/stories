<?php

declare(strict_types=1);

namespace backend\controllers\editor;

use backend\components\BaseController;
use backend\components\story\AbstractBlock;
use backend\models\SlidesOrder;
use backend\services\StoryEditorService;
use common\models\StorySlide;
use common\rbac\UserRoles;
use Yii;
use yii\base\InvalidConfigException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;

class SlideController extends BaseController
{
    private $editorService;

    public function __construct($id, $module, StoryEditorService $editorService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->editorService = $editorService;
    }

    /**
     * @throws BadRequestHttpException
     */
    public function beforeAction($action): bool
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return parent::beforeAction($action);
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::PERMISSION_MANAGE_TEST],
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
    public function actionSave(Request $request): array
    {
        $data = $request->rawBody;
        $slide = $this->editorService->processData($data);
        foreach ($slide->getBlocks() as $block) {
            if ($block->getType() === AbstractBlock::TYPE_HTML) {
                $data = str_replace(
                    'data-slide-view=""',
                    'data-slide-view="new-question"',
                    $data
                );
            }
            if ($block->getType() === AbstractBlock::TYPE_TABLE_OF_CONTENTS) {
                $data = str_replace(
                    'data-slide-view=""',
                    'data-slide-view="' . AbstractBlock::TYPE_TABLE_OF_CONTENTS . '"',
                    $data
                );
            }
        }

        $model = $this->findModel(StorySlide::class, $slide->getId());
        $model->updateData($data);
        return ['success' => true];
    }

    public function actionCreate(int $story_id, int $current_slide_id = -1, int $lesson_id = null): array
    {
        $slideID = $this->editorService->createSlide($story_id, $current_slide_id, $lesson_id);
        return ['success' => true, 'id' => $slideID];
    }

    /**
     * @throws NotFoundHttpException
     * @throws InvalidConfigException
     */
    public function actionDelete(int $slide_id): array
    {
        $slideModel = $this->findModel(StorySlide::class, $slide_id);
        $prevSlide = $slideModel->findPrevSlide();
        $prevSlideId = $prevSlide === null ? null : $prevSlide->id;
        $this->editorService->deleteSlide($slideModel);
        return ['success' => true, 'slide_id' => $prevSlideId];
    }

    public function actionCopy(int $slide_id, int $lesson_id = null): array
    {
        try {
            $slideID = $this->editorService->copySlide($slide_id, $lesson_id);
        }
        catch (\Exception $ex) {
            return ['success' => false, 'error' => $ex->getMessage()];
        }
        return ['success' => true, 'id' => $slideID];
    }

    public function actionSaveOrder(): array
    {
        $form = new SlidesOrder();
        $result = ['success' => false, 'errors' => ''];
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $form->saveSlidesOrder();
                $result['success'] = true;
            }
            catch (\Exception $ex) {
                $result['errors'] = $ex->getMessage();
            }
        }
        else {
            $result['errors'] = $form->errors;
        }
        return $result;
    }

    public function actionToggleVisible(int $slide_id)
    {
        /** @var StorySlide $model */
        $model = $this->findModel(StorySlide::class, $slide_id);
        $visible = $model->toggleVisible();
        $model->updateVisible($visible);
        return ['success' => true, 'status' => $visible];
    }
}

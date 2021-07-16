<?php

namespace backend\controllers\editor;

use backend\components\BaseController;
use backend\components\story\AbstractBlock;
use backend\models\SlidesOrder;
use backend\services\StoryEditorService;
use common\models\StorySlide;
use common\rbac\UserRoles;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class SlideController extends BaseController
{

    private $editorService;

    public function __construct($id, $module, StoryEditorService $editorService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->editorService = $editorService;
    }

    public function beforeAction($action)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return parent::beforeAction($action);
    }

    public function behaviors()
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

    public function actionSave()
    {
        $data = Yii::$app->request->rawBody;
        $slide = $this->editorService->processData($data);
        foreach ($slide->getBlocks() as $block) {
            if ($block->getType() === AbstractBlock::TYPE_HTML) {
                $data = str_replace('data-slide-view=""', 'data-slide-view="new-question"', $data);
            }
        }
        /** @var StorySlide $model */
        $model = $this->findModel(StorySlide::class, $slide->getId());
        $model->updateData($data);
        return ['success' => true];
    }

    public function actionCreate(int $story_id, int $current_slide_id = -1)
    {
        $slideID = $this->editorService->createSlide($story_id, $current_slide_id);
        return ['success' => true, 'id' => $slideID];
    }

    public function actionDelete(int $slide_id)
    {
        $slideModel = $this->findModel(StorySlide::class, $slide_id);
        $this->editorService->deleteSlide($slideModel);
        return ['success' => true];
    }

    public function actionCopy(int $slide_id)
    {
        try {
            $slideID = $this->editorService->copySlide($slide_id);
        }
        catch (\Exception $ex) {
            return ['success' => false, 'error' => $ex->getMessage()];
        }
        return ['success' => true, 'id' => $slideID];
    }

    public function actionSaveOrder()
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
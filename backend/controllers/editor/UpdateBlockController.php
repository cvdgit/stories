<?php

namespace backend\controllers\editor;

use backend\components\BaseController;
use backend\models\editor\BaseForm;
use backend\models\editor\ButtonForm;
use backend\models\editor\ImageForm;
use backend\models\editor\QuestionForm;
use backend\models\editor\TestForm;
use backend\models\editor\TextForm;
use backend\models\editor\TransitionForm;
use backend\models\editor\VideoForm;
use backend\services\StoryEditorService;
use common\models\StorySlide;
use common\rbac\UserRoles;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class UpdateBlockController extends BaseController
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
                    '*' => ['POST'],
                ],
            ],
        ];
    }

    public function actionText()
    {
        return $this->updateBlock(new TextForm());
    }

    public function actionImage()
    {
        return $this->updateBlock(new ImageForm());
    }

    public function actionButton()
    {
        return $this->updateBlock(new ButtonForm());
    }

    public function actionTransition()
    {
        return $this->updateBlock(new TransitionForm());
    }

    public function actionTest()
    {
        return $this->updateBlock(new TestForm());
    }

    public function actionVideo()
    {
        return $this->updateBlock(new VideoForm());
    }

    public function actionHtml()
    {
        return $this->updateBlock(new QuestionForm(['scenario' => 'update']));
    }

    private function updateBlock(BaseForm $form)
    {
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $slideModel = $this->findModel(StorySlide::class, $form->slide_id);
            $html = $this->editorService->updateBlock($form);
            $form->afterUpdate($slideModel);
            return ['success' => true, 'block_id' => $form->block_id, 'html' => $html];
        }
        return $form->getErrors();
    }
}

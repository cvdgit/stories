<?php

namespace backend\controllers\editor;

use backend\components\BaseController;
use backend\models\editor\BaseForm;
use backend\models\editor\ButtonForm;
use backend\models\editor\ImageForm;
use backend\models\editor\MentalMapForm;
use backend\models\editor\QuestionForm;
use backend\models\editor\TestForm;
use backend\models\editor\TextForm;
use backend\models\editor\TransitionForm;
use backend\models\editor\VideoForm;
use backend\services\StoryEditorService;
use common\models\StorySlide;
use common\rbac\UserRoles;
use Exception;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Request;
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

    public function actionMentalMap(Request $request): array
    {
        $form = new MentalMapForm();
        if ($form->load($request->post()) && $form->validate()) {
            try {
                /*$model = StorySlide::findSlide($form->slide_id);
                if ($model === null) {
                    throw new NotFoundHttpException('Slide not found');
                }*/
                $html = $this->editorService->updateMentalMapBlock($form);
                return ['success' => true, 'block_id' => $form->block_id, 'html' => $html];
            } catch(Exception $ex) {
                return ['success' => false, 'errors' => $ex->getMessage()];
            }
        }
        return ['success' => false, 'errors' => implode('<br/>', $form->getErrorSummary(true))];
    }

    private function updateBlock(BaseForm $form): array
    {
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $slideModel = $this->findModel(StorySlide::class, $form->slide_id);
            try {
                $html = $this->editorService->updateBlock($form);
                $form->afterUpdate($slideModel);
                return ['success' => true, 'block_id' => $form->block_id, 'html' => $html];
            }
            catch(Exception $ex) {
                return ['success' => false, 'errors' => $ex->getMessage()];
            }
        }
        return ['success' => false, 'errors' => implode('<br/>', $form->getErrorSummary(true))];
    }
}

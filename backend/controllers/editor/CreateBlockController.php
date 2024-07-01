<?php

declare(strict_types=1);

namespace backend\controllers\editor;

use backend\components\BaseController;
use backend\components\story\ButtonBlock;
use backend\components\story\HTMLBLock;
use backend\components\story\ImageBlock;
use backend\components\story\TestBlock;
use backend\components\story\TextBlock;
use backend\components\story\TransitionBlock;
use backend\components\story\VideoBlock;
use backend\components\story\VideoFileBlock;
use backend\models\editor\BaseForm;
use backend\models\editor\ButtonForm;
use backend\models\editor\ImageForm;
use backend\models\editor\ImageFromFileForm;
use backend\models\editor\ImageFromUrlForm;
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
use yii\base\InvalidConfigException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class CreateBlockController extends BaseController
{
    private $editorService;

    public function __construct($id, $module, StoryEditorService $editorService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->editorService = $editorService;
    }

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
                    '*' => ['POST'],
                ],
            ],
        ];
    }

    public function actionText()
    {
        return $this->createBlock(new TextForm(), TextBlock::class);
    }

    public function actionImage()
    {
        return $this->createBlock(new ImageForm(), ImageBlock::class);
    }

    public function actionImageFromFile()
    {
        return $this->createBlock(new ImageFromFileForm(), ImageBlock::class);
    }

    public function actionImageFromUrl()
    {
        return $this->createBlock(new ImageFromUrlForm(), ImageBlock::class);
    }

    public function actionButton()
    {
        return $this->createBlock(new ButtonForm(), ButtonBlock::class);
    }

    public function actionTransition()
    {
        return $this->createBlock(new TransitionForm(), TransitionBlock::class);
    }

    public function actionTest()
    {
        return $this->createBlock(new TestForm(), TestBlock::class);
    }

    public function actionVideo()
    {
        return $this->createBlock(new VideoForm(), VideoBlock::class);
    }

    public function actionVideofile()
    {
        return $this->createBlock(new VideoForm(), VideoFileBlock::class);
    }

    public function actionHtml()
    {
        return $this->createBlock(new QuestionForm(), HTMLBLock::class);
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    private function createBlock(BaseForm $form, string $blockClassName): array
    {
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $slideModel = $this->findModel(StorySlide::class, (int) $form->slide_id);
            try {
                $html = $this->editorService->createBlock($slideModel, $form, $blockClassName);
                $form->afterCreate($slideModel);
                return ['success' => true, 'html' => $html];
            }
            catch(Exception $ex) {
                return ['success' => false, 'errors' => $ex->getMessage()];
            }
        }
        return ['success' => false, 'errors' => implode('<br/>', $form->getErrorSummary(true))];
    }
}

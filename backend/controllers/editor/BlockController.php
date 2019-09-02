<?php


namespace backend\controllers\editor;


use backend\models\editor\ButtonForm;
use backend\services\StoryEditorService;
use common\models\StorySlideBlock;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class BlockController extends Controller
{

    protected $editorService;

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

    public function actionCreate(int $slide_id)
    {
        $model = StorySlideBlock::create($slide_id, 'New Button');
        $model->save();
        return ['success' => true];
    }

    public function actionUpdate(int $block_id)
    {
        $model = StorySlideBlock::findBlock($block_id);
        $form = new ButtonForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            return ['success' => true, 'result' => $this->editorService->newUpdateBlock($form)];
        }
        return ['success' => false, 'errors' => $form->errors];
    }

}
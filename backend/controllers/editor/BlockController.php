<?php

namespace backend\controllers\editor;

use backend\components\BaseController;
use backend\components\story\AbstractBlock;
use backend\components\story\reader\HtmlBlockReader;
use backend\models\ImageSlideBlock;
use backend\services\StoryEditorService;
use common\models\StorySlide;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Response;

class BlockController extends BaseController
{

    protected $editorService;

    public function __construct($id, $module, StoryEditorService $editorService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->editorService = $editorService;
    }

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return parent::beforeAction($action);
    }

    public function actionDelete(int $slide_id)
    {
        $slideModel = $this->findModel(StorySlide::class, $slide_id);
        $reader = new HtmlBlockReader(Yii::$app->request->rawBody);
        $block = $reader->load();
        if ($block->getType() === AbstractBlock::TYPE_IMAGE) {
            ImageSlideBlock::deleteImageBlock($slideModel->id, $block->getId());
        }
        if ($block->getType() === AbstractBlock::TYPE_VIDEO) {

        }
        if ($block->getType() === AbstractBlock::TYPE_HTML) {

        }
        return ['success' => true, 'block' => $block->getId()];
    }

    public function actionCopy(int $slide_id)
    {
        $reader = new HtmlBlockReader(Yii::$app->request->rawBody);
        $block = $reader->load();
        if ($block->getType() === AbstractBlock::TYPE_IMAGE) {

        }
        if ($block->getType() === AbstractBlock::TYPE_VIDEO) {

        }
        if ($block->getType() === AbstractBlock::TYPE_HTML) {

        }
        return ['success' => true, 'block' => $block->getId()];
    }
}

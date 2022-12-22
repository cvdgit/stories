<?php

namespace backend\controllers\editor;

use backend\components\BaseController;
use backend\components\story\AbstractBlock;
use backend\components\story\HTMLBLock;
use backend\components\story\ImageBlock;
use backend\components\story\reader\HtmlBlockReader;
use backend\components\story\TestBlock;
use backend\components\story\TestBlockContent;
use backend\models\ImageSlideBlock;
use backend\services\StoryEditorService;
use common\models\StorySlide;
use common\models\StoryStoryTest;
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
        /** @var StorySlide $slideModel */
        $slideModel = $this->findModel(StorySlide::class, $slide_id);
        $reader = new HtmlBlockReader(Yii::$app->request->rawBody);
        $block = $reader->load();
        if ($block->getType() === AbstractBlock::TYPE_IMAGE) {
            ImageSlideBlock::deleteImageBlock($slideModel->id, $block->getId());
        }
        if ($block->getType() === AbstractBlock::TYPE_VIDEO) {

        }
        if ($block->getType() === AbstractBlock::TYPE_HTML) {
            /** @var HTMLBLock $block */
            /** @var TestBlockContent $content */
            // Определить ИД теста из удаляемого блока по содержимому
            $content = $block->getContentObject(TestBlockContent::class);
            // Удалить связь истории и теста
            StoryStoryTest::deleteStoryTest($slideModel->story_id, $content->getTestID());
            // Установить для слайда тип по умолчанию вместо слайда с тестом
            $slideModel->setKindSlide();
        }
        if ($block->isTest()) {
            /** @var TestBlock $block */
            StoryStoryTest::deleteStoryTest($slideModel->story_id, $block->getTestID());
        }
        return ['success' => true, 'block' => $block->getId()];
    }

    public function actionCopy(int $slide_id, string $block_id)
    {
        /** @var StorySlide $slideModel */
        $slideModel = $this->findModel(StorySlide::class, $slide_id);
        $block = (new HtmlBlockReader(Yii::$app->request->rawBody))->load();
        if ($block->getType() === AbstractBlock::TYPE_IMAGE) {
            /** @var $block ImageBlock */
            $imageID = $block->getBlockAttribute('data-image-id');
            if ($imageID !== null) {
                $image = ImageSlideBlock::create($imageID, $slideModel->id, $block_id);
                $image->save();
            }
        }
        if ($block->getType() === AbstractBlock::TYPE_VIDEO) {

        }
        if ($block->getType() === AbstractBlock::TYPE_HTML) {

        }
        return ['success' => true, 'block' => $block->getId()];
    }
}

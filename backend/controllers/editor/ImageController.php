<?php


namespace backend\controllers\editor;


use backend\components\story\AbstractBlock;
use backend\components\story\ImageBlock;
use backend\components\story\reader\HTMLReader;
use backend\components\story\reader\HtmlSlideReader;
use backend\components\story\writer\HTMLWriter;
use backend\models\ImageForm;
use backend\services\ImageService;
use backend\services\StoryEditorService;
use common\models\Story;
use common\models\StorySlide;
use common\models\StorySlideImage;
use common\rbac\UserRoles;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\FileHelper;
use yii\web\Controller;
use yii\web\Response;

class ImageController extends Controller
{

    protected $imageService;
    protected $editorService;

    public function __construct($id, $module, ImageService $imageService, StoryEditorService $editorService, $config = [])
    {
        $this->imageService = $imageService;
        $this->editorService = $editorService;
        parent::__construct($id, $module, $config);
    }

    public function behaviors()
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
        ];
    }

    public function beforeAction($action)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return parent::beforeAction($action);
    }

    public function actionList(int $story_id)
    {
        $model = Story::findModel($story_id);
        $reader = new HTMLReader($model->slidesData(true));
        $story = $reader->load();
        $images = [];
        foreach ($story->getSlides() as $slide) {
            foreach ($slide->getBlocks() as $block) {
                if ($block->getType() === AbstractBlock::TYPE_IMAGE) {
                    $images[] = $block->getFilePath();
                }
            }
        }
        return $images;
    }

    public function actionCreate(int $slide_id, string $image)
    {

        $model = StorySlide::findSlide($slide_id);
        $reader = new HtmlSlideReader($model->data);
        $slide = $reader->load();

        /** @var $block ImageBlock */
        $block = $slide->createBlock(['class' => ImageBlock::class]);
        $block->setFilePath($image);
        $slide->addBlock($block);

        $writer = new HTMLWriter();
        $html = $writer->renderSlide($slide);

        $model->data = $html;
        $model->save(false, ['data']);

        return ['success' => true];
    }

    public function actionSet()
    {
        $form = new ImageForm();
        $success = false;
        if ($form->load(Yii::$app->request->post(), '') && $form->validate()) {

            $image = $this->imageService->createImage($form->slide_id, $form->collection_account, $form->collection_id, $form->collection_name, $form->content_url, $form->source_url);
            $path = $this->imageService->downloadImage($image->content_url, $image->hash, Yii::getAlias('@public/admin/upload/') . $image->folder);

            $block = new ImageBlock();
            $block->setFilePath(Yii::$app->urlManagerFrontend->createAbsoluteUrl(['image/view', 'id' => $image->hash]));
            [$imageWidth, $imageHeight] = getimagesize($path);
            $block->setWidth($imageWidth . 'px');
            $block->setHeight($imageHeight . 'px');

            $this->editorService->addImageBlockToSlide($form->slide_id, $block);
            $success = true;
        }

        return ['success' => $success];
    }

    public function actionGetUsedCollections(int $story_id)
    {
        $model = Story::findModel($story_id);
        return [
            'success' => true,
            'result' => StorySlideImage::usedCollections($model->id),
        ];
    }

}
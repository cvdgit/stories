<?php


namespace backend\controllers\editor;


use backend\components\story\AbstractBlock;
use backend\components\story\ImageBlock;
use backend\components\story\reader\HTMLReader;
use backend\components\story\reader\HtmlSlideReader;
use backend\components\story\writer\HTMLWriter;
use backend\models\editor\CropImageForm;
use backend\models\editor\ImageFromUrlForm;
use backend\models\ImageForm;
use backend\services\ImageService;
use backend\services\StoryEditorService;
use common\helpers\StoryHelper;
use common\models\Story;
use common\models\StorySlide;
use common\models\StorySlideImage;
use common\rbac\UserRoles;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\FileHelper;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;

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
        $result = ['success' => false, 'errors' => [], 'image' => []];
        if ($form->load(Yii::$app->request->post(), '') && $form->validate()) {
            $image = $this->imageService->createImage(
                $form->collection_account,
                $form->collection_id,
                $form->collection_name,
                $form->content_url,
                $form->source_url
            );
            $this->imageService->downloadImage($image->content_url, $image->hash, Yii::getAlias('@public/admin/upload/') . $image->folder);
            $result['image'] = [
                'url' => $image->imageUrl(),
                'id' => $image->hash,
            ];
            $result['success'] = true;
        }
        else {
            $result['errors'] = $form->errors;
        }

        return $result;
    }

    public function actionGetUsedCollections(int $story_id)
    {
        $model = Story::findModel($story_id);
        return [
            'success' => true,
            'result' => StorySlideImage::usedCollections($model->id),
        ];
    }

    public function actionUpdate(int $id)
    {
        Yii::$app->response->format = Response::FORMAT_HTML;
        $model = new ImageForm();
        $model->loadModel($id);
        //if ($model->load(Yii::$app->request->post()) && $model->validate()) {
        //    $model->saveVideo();
        //    return $this->refresh();
        //}
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete(int $id)
    {
        Yii::$app->response->format = Response::FORMAT_HTML;
        $model = StorySlideImage::findModel($id);
        if (!empty($model->block_id)) {
            $this->editorService->deleteBlock($model->slide_id, $model->block_id);
        }
        $model->delete();
        return $this->redirect(['image/index']);
    }

    public function actionGetImages(int $story_id)
    {
        $model = Story::findModel($story_id);
        return [
            'success' => true,
            'result' => StorySlideImage::storyImages($model->id),
        ];
    }

    public function actionBackup(int $image_id)
    {
        $model = StorySlideImage::findModel($image_id);
        $form = new ImageForm();
        $success = false;
        $errors = [];
        if ($form->load(Yii::$app->request->post(), '') && $form->validate()) {
            $image = $this->imageService->createImage(
                $form->collection_account,
                $form->collection_id,
                $form->collection_name,
                $form->content_url,
                $form->source_url
            );
            $this->imageService->downloadImage($image->content_url, $image->hash, Yii::getAlias('@public/admin/upload/') . $image->folder);
            $this->imageService->boundImage($model->id, $image->id);
            $success = true;
        }
        else {
            $errors = $form->errors;
        }
        return ['success' => $success, 'errors' => $errors];
    }

    public function actionDeleteFromStory(int $image_id, int $slide_id, string $block_id)
    {
        $image = StorySlideImage::findModel($image_id);
        $slide = StorySlide::findSlide($slide_id);

        $this->imageService->unlinkImage($image->id, $slide->id, $block_id);
        $this->editorService->deleteBlock($slide->id, $block_id);

        return ['success' => true, 'errors' => []];
    }

    public function actionCropperSave()
    {
        $form = new CropImageForm();
        if ($form->load(Yii::$app->request->post(), '') && $form->validate()) {
            $form->croppedImage = UploadedFile::getInstanceByName('croppedImage');

            $image = null;
            try {
                $image = StorySlideImage::findByHash($form->croppedImageID);
            }
            catch (\Exception $ex) {
            }

            if ($image !== null) {
                $image = $this->imageService->cropImage($form);
                $block = new ImageBlock();
                $block->setFilePath($image->imageUrl());
                $block->setSizeAndPosition($form->width . 'px', $form->height . 'px', $form->left . 'px', $form->top . 'px');
                $block->setImageSource(parse_url($image->source_url, PHP_URL_HOST));
                $this->editorService->addImageBlockToSlide($form->slide_id, $block);
                $this->imageService->linkImage($image->id, $form->slide_id, $block->getId());
            }
            else {

                $slide = StorySlide::findSlide($form->slide_id);
                $story = $slide->story;
                $storyImagesPath = StoryHelper::getImagesPath($story);
                $this->imageService->crop($form, $storyImagesPath . '/' . $form->croppedImageID);

                $block = new ImageBlock();
                $block->setFilePath(StoryHelper::getImagesPath($story, true) . '/' . $form->croppedImageID);
                $block->setSizeAndPosition($form->width . 'px', $form->height . 'px', $form->left . 'px', $form->top . 'px');
                $this->editorService->addImageBlockToSlide($form->slide_id, $block);
            }
        }
        return ['success' => true];
    }

    public function actionUploadImage()
    {
        $result = ['success' => false, 'errors' => [], 'image' => []];
        $form = new \backend\models\editor\ImageForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $form->image = UploadedFile::getInstance($form, 'image');
            if ($form->image !== null) {

                $image = $this->imageService->createImage(
                    '',
                    '',
                    '',
                    '',
                    ''
                );
                $form->upload($image->getFullPath());

                $result['image'] = [
                    'url' => $image->imageUrl(),
                    'id' => $image->hash,
                ];
                $result['success'] = true;
            }
        }
        else {
            $result['errors'] = $form->errors;
        }
        return $result;
    }

    public function actionSave()
    {
        $form = new \backend\models\editor\ImageForm();
        if ($form->load(Yii::$app->request->post(), '') && $form->validate()) {
            $block = new ImageBlock();

            if ($form->what === 'story') {

                $slide = StorySlide::findSlide($form->slide_id);
                $story = $slide->story;
                $storyImagesPath = Yii::getAlias('@public') . $form->imagePath;

                [$imageWidth, $imageHeight] = getimagesize($storyImagesPath);
                $ratio = $imageWidth / $imageHeight;
                if (ImageBlock::DEFAULT_IMAGE_WIDTH / ImageBlock::DEFAULT_IMAGE_HEIGHT > $ratio) {
                    $imageWidth = ImageBlock::DEFAULT_IMAGE_HEIGHT * $ratio;
                    $imageHeight = ImageBlock::DEFAULT_IMAGE_HEIGHT;
                } else {
                    $imageHeight = ImageBlock::DEFAULT_IMAGE_WIDTH / $ratio;
                    $imageWidth = ImageBlock::DEFAULT_IMAGE_WIDTH;
                }

                $block->setFilePath($form->imagePath);
                $block->setWidth($imageWidth . 'px');
                $block->setHeight($imageHeight . 'px');
                $this->editorService->addImageBlockToSlide($form->slide_id, $block);
            }
            else {

                $image = StorySlideImage::findByHash($form->imageID);

                $block->setFilePath(Yii::$app->urlManagerFrontend->createAbsoluteUrl(['image/view', 'id' => $image->hash]));

                [$imageWidth, $imageHeight] = getimagesize($image->getFullPath());
                $ratio = $imageWidth / $imageHeight;
                if (ImageBlock::DEFAULT_IMAGE_WIDTH / ImageBlock::DEFAULT_IMAGE_HEIGHT > $ratio) {
                    $imageWidth = ImageBlock::DEFAULT_IMAGE_HEIGHT * $ratio;
                    $imageHeight = ImageBlock::DEFAULT_IMAGE_HEIGHT;
                } else {
                    $imageHeight = ImageBlock::DEFAULT_IMAGE_WIDTH / $ratio;
                    $imageWidth = ImageBlock::DEFAULT_IMAGE_WIDTH;
                }

                $block->setWidth($imageWidth . 'px');
                $block->setHeight($imageHeight . 'px');
                if ($image->source_url !== '') {
                    $block->setImageSource(parse_url($image->source_url, PHP_URL_HOST));
                }
                $this->editorService->addImageBlockToSlide($form->slide_id, $block);

                $this->imageService->linkImage($image->id, $form->slide_id, $block->getId());
            }
        }
        return ['success' => true];
    }

    public function actionImageFromUrl()
    {
        $form = new ImageFromUrlForm();
        $result = ['success' => false, 'errors' => [], 'image' => []];
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $image = $this->imageService->createImage(
                '',
                '',
                '',
                $form->url,
                $form->url
            );
            $this->imageService->downloadImage($image->source_url, $image->hash, Yii::getAlias('@public/admin/upload/') . $image->folder);
            $result['image'] = [
                'url' => $image->imageUrl(),
                'id' => $image->hash,
            ];
            $result['success'] = true;
        }
        else {
            $result['errors'] = $form->errors;
        }
        return $result;
    }

}
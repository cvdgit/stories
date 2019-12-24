<?php


namespace backend\controllers\editor;


use backend\components\story\AbstractBlock;
use backend\components\story\ImageBlock;
use backend\components\story\reader\HTMLReader;
use backend\components\story\reader\HtmlSlideReader;
use backend\components\story\writer\HTMLWriter;
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

    public function actionSet(int $slide_id, string $url)
    {

        $model = StorySlide::findSlide($slide_id);
        $storyBaseModel = $model->story->getBaseModel();



        $image = new StorySlideImage();
        $image->hash = Yii::$app->security->generateRandomString();
        $image->source_url = $url;
        $image->folder = Yii::$app->formatter->asDate('now', 'MM-yyyy');
        if ($image->save()) {

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
            curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36');
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $raw = curl_exec($ch);

            $info = curl_getinfo($ch);
            curl_close($ch);

            $ext = FileHelper::getExtensionsByMimeType($info['content_type']);
            $imageFileName = $image->hash . '.' . $ext[1];


            $path = Yii::getAlias('@public/admin/upload/') . $image->folder;
            FileHelper::createDirectory($path);
            $path .= '/' . $imageFileName;

            $fp = fopen($path, 'xb');
            fwrite($fp, $raw);
            fclose($fp);


            $reader = new HtmlSlideReader($model->data);
            $slide = $reader->load();

            /** @var $block ImageBlock */
            $block = $slide->createBlock(['class' => ImageBlock::class]);

            [$imageWidth, $imageHeight] = getimagesize($path);
            $block->setFilePath(Yii::$app->urlManagerFrontend->createAbsoluteUrl(['image/view', 'id' => $image->hash]));
            $block->setWidth($imageWidth);
            $block->setHeight($imageHeight);
            $slide->addBlock($block);

            $writer = new HTMLWriter();
            $html = $writer->renderSlide($slide);

            $model->data = $html;
            $model->save(false, ['data']);
        }

        return ['success' => true];
    }

}
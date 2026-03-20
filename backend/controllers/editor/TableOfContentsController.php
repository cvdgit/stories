<?php

declare(strict_types=1);

namespace backend\controllers\editor;

use backend\components\BaseController;
use backend\SlideEditor\TableOfContents\Image\CardImageForm;
use common\components\ModelDomainException;
use common\models\StorySlideImage;
use common\services\FileUploadService;
use DomainException;
use Ramsey\Uuid\Uuid;
use Yii;
use yii\base\Exception;
use yii\helpers\FileHelper;
use yii\web\Request;
use yii\web\Response;
use yii\web\UploadedFile;

class TableOfContentsController extends BaseController
{
    /**
     * @var FileUploadService
     */
    private $fileUploadService;

    public function __construct($id, $module, FileUploadService $fileUploadService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->fileUploadService = $fileUploadService;
    }

    public function actionCardImage(Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $replaceForm = new CardImageForm();
        if ($replaceForm->load($request->post(), '')) {
            $replaceForm->image = UploadedFile::getInstanceByName('image');
            if (!$replaceForm->validate()) {
                return ['success' => false, 'message' => 'Not valid', $replaceForm->errors];
            }

            $file = null;
            $folder = Yii::$app->params['images.root'][4] . $replaceForm->card_id;
            try {
                $file = $this->fileUploadService->uploadFile(
                    $folder,
                    $replaceForm->image
                );

                $imageId = Uuid::uuid4()->toString();
                $image = StorySlideImage::create(
                    $imageId,
                    $file->getFileName(),
                    $file->getBaseFolder(),
                    $file->getExtension(),
                    4
                );
                if (!$image->save()) {
                    throw ModelDomainException::create($image);
                }
            } catch (Exception $exception) {
                if ($file !== null) {
                    $this->fileUploadService->deleteFile($folder, $file->getFileName(), $file->getExtension());
                }
            }

            return [
                'success' => true,
                'url' => '/upload/table-of-contents-cards/' . $replaceForm->card_id . '/' . $file->getFileName(),
                'originalUrl' => $file->getFileName(),
                'thumbnail' => Yii::$app->urlManagerFrontend->createAbsoluteUrl(
                    ['image/get', 'path' => '/table-of-contents-cards/' . $replaceForm->card_id . '/' . $file->getFileName()],
                ),
            ];
        }
        return ['success' => false, 'message' => 'No data'];
    }
}

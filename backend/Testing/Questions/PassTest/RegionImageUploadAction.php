<?php

declare(strict_types=1);

namespace backend\Testing\Questions\PassTest;

use common\models\StorySlideImage;
use Ramsey\Uuid\Uuid;
use Yii;
use yii\base\Action;
use yii\log\LogRuntimeException;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;
use yii\web\UploadedFile;

class RegionImageUploadAction extends Action
{
    private $handler;

    public function __construct($id, $controller, RegionImageUploadHandler $handler, $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->handler = $handler;
    }

    public function run(Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $uploadForm = new RegionImageUploadForm();
        if ($uploadForm->load($request->post(), '')) {

            $uploadForm->image = UploadedFile::getInstanceByName('image');
            if (!$uploadForm->validate()) {
                return ['success' => false, 'message' => 'Not valid'];
            }

            $imageId = Uuid::uuid4()->toString();

            try {
                $this->handler->handle(new RegionImageUploadCommand(
                    $imageId,
                    Yii::$app->params['images.root'][3] . $uploadForm->testing_id,
                    $uploadForm->image,
                    $uploadForm->fragment_id,
                    (int) $uploadForm->testing_id
                ));

                $image = StorySlideImage::findByHash($imageId);
                if ($image === null) {
                    throw new LogRuntimeException('Ошибка при создании файла');
                }

                $data = [
                    'id' => $imageId,
                    'url' => \Yii::$app->urlManagerFrontend->createUrl(['/image/view', 'id' => $imageId]),
                ];

                [$width, $height] = getimagesize($image->getImagePath());
                $data += ['width' => $width, 'height' => $height];

                return ['success' => true, 'data' => $data];
            } catch (\Exception $exception) {
                \Yii::$app->errorHandler->logException($exception);
                return ['success' => false, 'message' => $exception->getMessage()];
            }
        }
        return ['success' => false, 'message' => 'No data'];
    }
}

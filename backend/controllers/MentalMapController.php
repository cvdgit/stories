<?php

declare(strict_types=1);

namespace backend\controllers;

use backend\MentalMap\MentalMap;
use backend\MentalMap\MentalMapImage;
use backend\MentalMap\MentalMapImageForm;
use common\rbac\UserRoles;
use DomainException;
use Ramsey\Uuid\Uuid;
use Yii;
use yii\base\Exception;
use yii\db\Query;
use yii\debug\Module;
use yii\filters\AccessControl;
use yii\helpers\FileHelper;
use yii\imagine\Image;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\web\View;
use yii\web\User as WebUser;

class MentalMapController extends Controller
{
    public $layout = 'mental-map';
    public $enableCsrfValidation = false;

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::PERMISSION_EDITOR_ACCESS],
                    ],
                ],
            ],
        ];
    }

    /**
     * @throws BadRequestHttpException
     */
    public function actionEditor(string $id, WebUser $user, Request $request): string
    {
        $mentalMap = MentalMap::findOne($id);
        if ($mentalMap === null) {
            if (!Uuid::isValid($id)) {
                throw new BadRequestHttpException('Id not valid');
            }

            $name = 'Mental Map';
            $payload = [
                'id' => $id,
                'name' => $name,
                'text' => '',
                'map' => [
                    'url' => null,
                    'width' => null,
                    'height' => null,
                    'images' => [],
                ],
            ];

            $mentalMap = MentalMap::create($id, $name, $payload, $user->getId());
            if (!$mentalMap->save()) {
                throw new BadRequestHttpException('Mental Map save exception');
            }
        }

        if (class_exists(Module::class)) {
            $this->view->off(View::EVENT_END_BODY, [Module::getInstance(), 'renderToolbar']);
        }

        return $this->render('editor', [
            'name' => $mentalMap->name,
            'id' => $mentalMap->uuid,
            'returnUrl' => $request->referrer,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionGet(string $id, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $mentalMap = MentalMap::findOne($id);
        if ($mentalMap === null) {
            throw new NotFoundHttpException('Mental Map not found');
        }

        $ids = array_map(static function (array $image): string {
            return $image['id'];
        }, $mentalMap->getImages());

        $images = (new Query())
            ->select('*')
            ->from(['t' => 'mental_map_image'])
            ->andWhere("t.type = 'image'")
            ->andWhere(['not in', 't.uuid', $ids])
            ->all();

        $images = array_filter($images, static function(array $image): bool {
            return file_exists(Yii::getAlias('@public') . '/' . $image['key']);
        });

        return [
            'mentalMap' => $mentalMap->payload,
            'images' => array_map(static function(array $image): array {
                [$imageWidth, $imageHeight] = getimagesize(Yii::getAlias('@public') . '/' . $image['key']);
                return [
                    'id' => $image['uuid'],
                    'fileName' => $image['name'],
                    'url' => '/' . $image['key'],
                    'width' => $imageWidth,
                    'height' => $imageHeight,
                ];
            }, $images),
        ];
    }

    /**
     * @throws Exception
     */
    public function actionImage(Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $imageForm = new MentalMapImageForm();
        if ($imageForm->load($request->post(), '')) {
            $imageForm->image = UploadedFile::getInstanceByName('image');
            if (!$imageForm->validate()) {
                return ['success' => false, 'message' => 'Not valid', $imageForm->errors];
            }

            $uploadsDir = Yii::getAlias('@public/upload');
            $mentalMapDir = $uploadsDir . '/mental-map/' . $imageForm->mental_map_id;
            FileHelper::createDirectory($mentalMapDir);

            $thumbnailDir = $mentalMapDir . '/thumbs';
            FileHelper::createDirectory($thumbnailDir);

            $name = str_replace([' ', '"', '\'', '&', '/', '\\', '?', '#'], '-', $imageForm->image->baseName);
            $fileName = $name . '.' . $imageForm->image->extension;

            $newFileName = substr(str_replace('-', '', Uuid::uuid4()->toString()) . '-' . $fileName, 12);

            try {
                if (!$imageForm->image->saveAs($mentalMapDir . '/' . $newFileName)) {
                    throw new DomainException('File upload error');
                }

                $imageId = empty($imageForm->image_item_id) ? Uuid::uuid4()->toString() : $imageForm->image_item_id;
                $originalImage = MentalMapImage::create(
                    $imageId,
                    $fileName,
                    $imageForm->type,
                    'upload/mental-map/' . $imageForm->mental_map_id . ($imageForm->type === 'image' ? '/thumbs/' : '/') . $newFileName,
                    $imageForm->mental_map_id,
                );
                if (!$originalImage->save()) {
                    throw new DomainException('Image save exception');
                }

                $url = '/upload/mental-map/' . $imageForm->mental_map_id . '/' . $newFileName;
                if ($imageForm->type === 'image') {
                    $width = 150;
                    Image::thumbnail($mentalMapDir . '/' . $newFileName, $width, null)
                        ->save($thumbnailDir . '/' . $newFileName, ['quality' => 100]);
                    $url = '/upload/mental-map/' . $imageForm->mental_map_id . '/thumbs/' . $newFileName;
                }

                [$imageWidth, $imageHeight, $imageType] = getimagesize(Yii::getAlias('@public') . $url);

                return [
                    'success' => true,
                    'url' => $url,
                    'width' => $imageWidth,
                    'height' => $imageHeight,
                ];
            } catch (\Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                return ['success' => false, 'message' => 'Image upload error'];
            }
        }
        return ['success' => false, 'message' => 'No data'];
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionUpdateMap(Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $payload = $request->post('payload');
        $mentalMapModel = MentalMap::findOne($payload['id']);
        if ($mentalMapModel === null) {
            throw new NotFoundHttpException('Mental Map not found');
        }
        try {
            $mentalMapModel->updateMap($payload['map']['url'], $payload['map']['width'], $payload['map']['height'], $payload['map']['images']);
            if (!$mentalMapModel->save()) {
                throw new DomainException('Mental Map map image update error');
            }
            return ['success' => true];
        } catch (\Exception $exception) {
            Yii::$app->errorHandler->logException($exception);
            return ['success' => false, 'message' => $exception->getMessage()];
        }
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionUpdateMapText(Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $payload = $request->post('payload');
        $mentalMapModel = MentalMap::findOne($payload['id']);
        if ($mentalMapModel === null) {
            throw new NotFoundHttpException('Mental Map not found');
        }
        try {
            $mentalMapModel->updateMapText($payload['text']);
            if (!$mentalMapModel->save()) {
                throw new DomainException('Mental Map map text update error');
            }
            return ['success' => true];
        } catch (\Exception $exception) {
            Yii::$app->errorHandler->logException($exception);
            return ['success' => false, 'message' => $exception->getMessage()];
        }
    }
}

<?php

declare(strict_types=1);

namespace backend\SlideEditor\CreateMentalMap;

use backend\MentalMap\MentalMap;
use backend\services\ImageService;
use backend\services\StoryEditorService;
use backend\services\StorySlideService;
use common\models\Story;
use common\models\StorySlide;
use common\services\TransactionManager;
use DomainException;
use Exception;
use Ramsey\Uuid\Uuid;
use Yii;
use yii\base\Action;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;
use yii\web\User as WebUser;

class CreateMentalMapAction extends Action
{
    /**
     * @var StorySlideService
     */
    private $storySlideService;
    /**
     * @var StoryEditorService
     */
    private $storyEditorService;
    /**
     * @var TransactionManager
     */
    private $transactionManager;
    /**
     * @var ImageService
     */
    private $imageService;

    public function __construct(
        $id,
        $controller,
        StorySlideService $storySlideService,
        StoryEditorService $storyEditorService,
        TransactionManager $transactionManager,
        ImageService $imageService,
        $config = []
    ) {
        parent::__construct($id, $controller, $config);
        $this->storySlideService = $storySlideService;
        $this->storyEditorService = $storyEditorService;
        $this->transactionManager = $transactionManager;
        $this->imageService = $imageService;
    }

    /**
     * @throws NotFoundHttpException
     */
    public function run(int $story_id, int $current_slide_id, Request $request, Response $response, WebUser $user): array
    {
        $response->format = Response::FORMAT_JSON;

        $storyModel = Story::findOne($story_id);
        if ($storyModel === null) {
            throw new NotFoundHttpException('История не найдена');
        }

        $currentSlideModel = StorySlide::findOne($current_slide_id);
        if ($currentSlideModel === null) {
            throw new NotFoundHttpException('Слайд не найден');
        }

        $jsonBody = Json::decode($request->rawBody, false);
        $content = $jsonBody->content;
        $mapImageUrl = $jsonBody->image;

        try {
            $this->transactionManager->wrap(function () use ($content, $storyModel, $currentSlideModel, $user, $mapImageUrl) {

                $slideModel = $this->storySlideService->create($storyModel->id, 'empty', StorySlide::KIND_MENTAL_MAP);
                $slideModel->number = $currentSlideModel->number + 1;
                Story::insertSlideNumber($storyModel->id, $currentSlideModel->number);
                if (!$slideModel->save()) {
                    throw new DomainException(
                        'Can\'t be saved StorySlide model. Errors: ' . implode(', ', $slideModel->getFirstErrors()),
                    );
                }

                $mentalMapId = Uuid::uuid4()->toString();
                $name = 'Ментальная карта';

                $imageUrl = null;
                $imageWidth = null;
                $imageHeight = null;
                if ($mapImageUrl) {
                    $uploadsDir = Yii::getAlias('@public/upload');
                    $mentalMapDir = $uploadsDir . '/mental-map/' . $mentalMapId;
                    FileHelper::createDirectory($mentalMapDir);

                    $path = $this->imageService->downloadImage($mapImageUrl, $mentalMapDir);

                    $imageUrl = '/upload/mental-map/' . $mentalMapId . '/' . pathinfo($path, PATHINFO_BASENAME);
                    [$imageWidth, $imageHeight] = getimagesize($path);
                }

                $payload = [
                    'id' => $mentalMapId,
                    'name' => $name,
                    'text' => $content,
                    'map' => [
                        'url' => $imageUrl,
                        'width' => $imageWidth,
                        'height' => $imageHeight,
                        'images' => [],
                    ],
                ];
                $mentalMap = MentalMap::create($mentalMapId, $name, $payload, $user->getId());
                if (!$mentalMap->save()) {
                    throw new BadRequestHttpException('Mental Map save exception');
                }

                $data = $this->storyEditorService->getSlideWithMentalMapBlockContent($slideModel->id, $mentalMapId);
                $slideModel->updateData($data);
                if (!$slideModel->save()) {
                    throw new DomainException(
                        'Can\'t be saved StorySlide model. Errors: ' . implode(', ', $slideModel->getFirstErrors()),
                    );
                }
            });

            return ["success" => true];
        } catch (Exception $exception) {
            Yii::$app->errorHandler->logException($exception);
            return ["success" => false, "message" => $exception->getMessage()];
        }
    }
}

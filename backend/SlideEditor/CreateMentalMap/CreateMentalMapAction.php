<?php

declare(strict_types=1);

namespace backend\SlideEditor\CreateMentalMap;

use backend\MentalMap\MentalMap;
use backend\models\editor\MentalMapForm;
use backend\services\ImageService;
use backend\services\StoryEditorService;
use backend\services\StorySlideService;
use common\helpers\Url;
use common\models\Story;
use common\models\StorySlide;
use common\services\TransactionManager;
use DomainException;
use Exception;
use Ramsey\Uuid\Uuid;
use Yii;
use yii\base\Action;
use yii\helpers\FileHelper;
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

        $mentalMapForm = new MentalMapForm();
        if ($mentalMapForm->load($request->post())) {
            if (!$mentalMapForm->validate()) {
                return ['success' => false, 'message' => 'Not valid'];
            }

            $newSlideId = null;
            try {
                $this->transactionManager->wrap(function () use ($mentalMapForm, &$newSlideId, $storyModel, $currentSlideModel, $user) {

                    $slideModel = $this->storySlideService->create($storyModel->id, 'empty', StorySlide::KIND_MENTAL_MAP);
                    $slideModel->number = $currentSlideModel->number + 1;
                    Story::insertSlideNumber($storyModel->id, $currentSlideModel->number);
                    if (!$slideModel->save()) {
                        throw new DomainException(
                            'Can\'t be saved StorySlide model. Errors: ' . implode(', ', $slideModel->getFirstErrors()),
                        );
                    }

                    $mentalMapId = Uuid::uuid4()->toString();

                    $imageUrl = null;
                    $imageWidth = null;
                    $imageHeight = null;

                    $mapImageUrl = $mentalMapForm->image;
                    if ($mentalMapForm->use_slide_image === '1' && $mapImageUrl) {
                        $uploadsDir = Yii::getAlias('@public/upload');
                        $mentalMapDir = $uploadsDir . '/mental-map/' . $mentalMapId;
                        FileHelper::createDirectory($mentalMapDir);

                        if (filter_var($mapImageUrl, FILTER_VALIDATE_URL) === false) {
                            $mapImageUrl = Url::homeUrl() . $mapImageUrl;
                        }

                        $path = $this->imageService->downloadImage($mapImageUrl, $mentalMapDir);

                        $imageUrl = '/upload/mental-map/' . $mentalMapId . '/' . pathinfo($path, PATHINFO_BASENAME);
                        [$imageWidth, $imageHeight] = getimagesize($path);
                    } else {
                        $imageUrl = '/img/mental_map_blank.jpg';
                        $imageWidth = 1280;
                        $imageHeight = 720;
                    }

                    $payload = [
                        'id' => $mentalMapId,
                        'name' => $mentalMapForm->name,
                        'text' => $mentalMapForm->texts,
                        'treeView' => $mentalMapForm->tree_view === '1',
                        'map' => [
                            'url' => $imageUrl,
                            'width' => $imageWidth,
                            'height' => $imageHeight,
                            'images' => [],
                        ],
                    ];
                    $mentalMap = MentalMap::create($mentalMapId, $mentalMapForm->name, $payload, $user->getId());
                    if (!$mentalMap->save()) {
                        throw new BadRequestHttpException('Mental Map save exception');
                    }

                    $data = $this->storyEditorService->getSlideWithMentalMapBlockContent($slideModel->id, $mentalMapId, 'mental-map', $mentalMapForm->required === '1');
                    $slideModel->updateData($data);
                    if (!$slideModel->save()) {
                        throw new DomainException(
                            'Can\'t be saved StorySlide model. Errors: ' . implode(', ', $slideModel->getFirstErrors()),
                        );
                    }
                    $newSlideId = $slideModel->id;
                });

                return ["success" => true, 'slide_id' => $newSlideId];
            } catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                return ["success" => false, "message" => $exception->getMessage()];
            }
        }
        return ['success' => false, 'message' => 'No data'];
    }
}

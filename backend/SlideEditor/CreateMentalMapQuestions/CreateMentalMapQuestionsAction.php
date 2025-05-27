<?php

declare(strict_types=1);

namespace backend\SlideEditor\CreateMentalMapQuestions;

use backend\MentalMap\MentalMap;
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
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;
use yii\web\User as WebUser;

class CreateMentalMapQuestionsAction extends Action
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

    public function __construct(
        $id,
        $controller,
        StorySlideService $storySlideService,
        StoryEditorService $storyEditorService,
        TransactionManager $transactionManager,
        $config = []
    ) {
        parent::__construct($id, $controller, $config);
        $this->storySlideService = $storySlideService;
        $this->storyEditorService = $storyEditorService;
        $this->transactionManager = $transactionManager;
    }

    /**
     * @throws NotFoundHttpException
     */
    public function run(
        int $story_id,
        int $current_slide_id,
        Request $request,
        Response $response,
        WebUser $user
    ): array {
        $response->format = Response::FORMAT_JSON;

        $storyModel = Story::findOne($story_id);
        if ($storyModel === null) {
            throw new NotFoundHttpException('История не найдена');
        }

        $currentSlideModel = StorySlide::findOne($current_slide_id);
        if ($currentSlideModel === null) {
            throw new NotFoundHttpException('Слайд не найден');
        }

        $json = Json::decode($request->rawBody);

        $createForm = new CreateMentalMapQuestionsForm();
        if ($createForm->load($json, '')) {
            if (!$createForm->validate()) {
                return ['success' => false, 'message' => 'Not valid'];
            }

            $newSlideId = null;

            $sourceMentalMap = MentalMap::findOne($createForm->mentalMapId);
            if ($sourceMentalMap === null) {
                return ['success' => false, 'message' => 'Source mental map not found'];
            }

            try {
                $this->transactionManager->wrap(
                    function () use ($createForm, &$newSlideId, $storyModel, $currentSlideModel, $user) {
                        $slideModel = $this->storySlideService->create(
                            $storyModel->id,
                            'empty',
                            StorySlide::KIND_MENTAL_MAP,
                        );

                        $slideModel->number = $currentSlideModel->number + 1;
                        Story::insertSlideNumber($storyModel->id, $currentSlideModel->number);
                        if (!$slideModel->save()) {
                            throw new DomainException(
                                'Can\'t be saved StorySlide model. Errors: ' . implode(
                                    ', ',
                                    $slideModel->getFirstErrors(),
                                ),
                            );
                        }

                        $mentalMapId = Uuid::uuid4()->toString();
                        $name = 'Ментальная карта с вопросами';
                        $payload = [
                            'id' => $mentalMapId,
                            'name' => $name,
                            'questions' => $createForm->fragments,
                        ];

                        $mentalMap = MentalMap::createMentalMapQuestions(
                            $mentalMapId,
                            $name,
                            $payload,
                            $user->getId(),
                            $createForm->mentalMapId,
                        );
                        if (!$mentalMap->save()) {
                            throw new BadRequestHttpException('Mental Map save exception');
                        }

                        $data = $this->storyEditorService->getSlideWithMentalMapBlockContent(
                            $slideModel->id,
                            $mentalMapId,
                            'mental-map-questions',
                            $createForm->required === '1',
                        );
                        $slideModel->updateData($data);
                        if (!$slideModel->save()) {
                            throw new DomainException(
                                'Can\'t be saved StorySlide model. Errors: ' . implode(
                                    ', ',
                                    $slideModel->getFirstErrors(),
                                ),
                            );
                        }
                        $newSlideId = $slideModel->id;
                    },
                );

                return ["success" => true, 'slide_id' => $newSlideId];
            } catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                return ["success" => false, "message" => $exception->getMessage()];
            }
        }

        return ['success' => false, 'message' => 'No data'];
    }
}

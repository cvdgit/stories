<?php

declare(strict_types=1);

namespace backend\SlideEditor\CreateRetelling;

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
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;
use yii\web\User as WebUser;

class CreateRetellingAction extends Action
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

        $retellingForm = new CreateRetellingForm();
        if ($retellingForm->load($request->post(), '')) {
            if (!$retellingForm->validate()) {
                return ['success' => false, 'message' => 'Not valid'];
            }

            $newSlideId = null;
            try {
                $this->transactionManager->wrap(function () use ($retellingForm, &$newSlideId, $storyModel, $currentSlideModel, $user) {

                    $slideModel = $this->storySlideService->create($storyModel->id, 'empty', StorySlide::KIND_RETELLING);

                    $slideModel->number = $currentSlideModel->number + 1;
                    Story::insertSlideNumber($storyModel->id, $currentSlideModel->number);
                    if (!$slideModel->save()) {
                        throw new DomainException(
                            'Can\'t be saved StorySlide model. Errors: ' . implode(', ', $slideModel->getFirstErrors()),
                        );
                    }

                    $createCommand = Yii::$app->db->createCommand();
                    $createCommand->insert('retelling', [
                        'id' => $retellingId = Uuid::uuid4()->toString(),
                        'slide_id' => $currentSlideModel->id,
                        'name' => 'Перескажите текст' . ($retellingForm->with_questions === '1' ? ' (с вопросами)' : ''),
                        'questions' => $retellingForm->questions,
                        'with_questions' => $retellingForm->with_questions,
                        'created_by' => $user->getId(),
                        'created_at' => time(),
                        'updated_at' => time(),
                    ]);
                    $createCommand->execute();

                    $data = $this->storyEditorService->getSlideWithRetellingBlockContent($slideModel->id, $retellingId, $retellingForm->required === '1');
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

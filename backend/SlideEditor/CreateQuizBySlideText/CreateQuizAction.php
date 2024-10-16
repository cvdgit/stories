<?php

declare(strict_types=1);

namespace backend\SlideEditor\CreateQuizBySlideText;

use backend\JsonSchema\QuestionsJsonSchemaValidator;
use backend\services\ImportQuestionService;
use backend\services\StoryEditorService;
use backend\services\StoryLinksService;
use backend\services\StorySlideService;
use common\models\Story;
use common\models\StorySlide;
use common\models\StoryTest;
use common\services\TransactionManager;
use yii\base\Action;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;

class CreateQuizAction extends Action
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
     * @var ImportQuestionService
     */
    private $importQuestionService;
    /**
     * @var StoryLinksService
     */
    private $storyLinksService;

    public function __construct(
        $id,
        $controller,
        StorySlideService $storySlideService,
        StoryEditorService $storyEditorService,
        TransactionManager $transactionManager,
        ImportQuestionService $importQuestionService,
        StoryLinksService $storyLinksService,
        $config = []
    )
    {
        parent::__construct($id, $controller, $config);
        $this->storySlideService = $storySlideService;
        $this->storyEditorService = $storyEditorService;
        $this->transactionManager = $transactionManager;
        $this->importQuestionService = $importQuestionService;
        $this->storyLinksService = $storyLinksService;
    }

    /**
     * @throws NotFoundHttpException
     */
    public function run(int $story_id, int $current_slide_id, Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;

        $storyModel = Story::findOne($story_id);
        if ($storyModel === null) {
            throw new NotFoundHttpException("История не найдена");
        }

        $currentSlideModel = StorySlide::findOne($current_slide_id);
        if ($currentSlideModel === null) {
            throw new NotFoundHttpException("Слайд не найден");
        }

        $jsonBody = Json::decode($request->rawBody, false);
        $content = $jsonBody->content;

        $schemaValidator = new QuestionsJsonSchemaValidator();
        $schemaValidator->validate($content);

        if ($schemaValidator->isValid()) {
            try {

                $this->transactionManager->wrap(function() use ($content, $storyModel, $currentSlideModel) {

                    $testModel = StoryTest::create("Вопросы для закрепления материала", "Вопросы для закрепления материала", "Вопросы для закрепления материала", "");
                    $testModel->repeat = 1;
                    if (!$testModel->save()) {
                        throw new \DomainException("Ошибка при создании теста");
                    }
                    $this->importQuestionService->createFromJson($testModel->id, $content);

                    $slideModel = $this->storySlideService->create($storyModel->id, 'New questions', StorySlide::KIND_QUESTION);
                    $slideModel->number = $currentSlideModel->number + 1;
                    Story::insertSlideNumber($storyModel->id, $currentSlideModel->number);
                    if (!$slideModel->save()) {
                        throw new \DomainException('Can\'t be saved StorySlide model. Errors: '. implode(', ', $slideModel->getFirstErrors()));
                    }
                    $slideModel->updateData($this->storyEditorService->createQuestionBlock($slideModel->id, $testModel->id));

                    $this->storyLinksService->createTestLink($storyModel->id, $testModel->id);
                });

                return ["success" => true];
            } catch (\Exception $exception) {
                \Yii::$app->errorHandler->logException($exception);
                return ["success" => false, "message" => $exception->getMessage()];
            }
        } else {
            return ["success" => false, "message" => "JSON not valid"];
        }
    }
}

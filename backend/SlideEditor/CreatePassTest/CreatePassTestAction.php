<?php

declare(strict_types=1);

namespace backend\SlideEditor\CreatePassTest;

use backend\models\pass_test\PassTestForm;
use backend\services\PassTestService;
use backend\services\StoryEditorService;
use backend\services\StoryLinksService;
use backend\services\StorySlideService;
use common\models\Story;
use common\models\StorySlide;
use common\models\StoryTest;
use common\services\TransactionManager;
use Ramsey\Uuid\Uuid;
use yii\base\Action;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;

class CreatePassTestAction extends Action
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
     * @var StoryLinksService
     */
    private $storyLinksService;
    /**
     * @var PassTestService
     */
    private $passTestService;

    public function __construct(
        $id,
        $controller,
        StorySlideService $storySlideService,
        StoryEditorService $storyEditorService,
        TransactionManager $transactionManager,
        StoryLinksService $storyLinksService,
        PassTestService $passTestService,
        $config = []
    )
    {
        parent::__construct($id, $controller, $config);
        $this->storySlideService = $storySlideService;
        $this->storyEditorService = $storyEditorService;
        $this->transactionManager = $transactionManager;
        $this->storyLinksService = $storyLinksService;
        $this->passTestService = $passTestService;
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

        $pattern = '#<(strong|b).*?>([^</]+)</\1>#ui';
        $content = mb_convert_encoding($content, "UTF-8");

        $fragments = [];
        $content = preg_replace_callback($pattern, static function(array $matches) use (&$fragments) {
            $fragmentId = Uuid::uuid4()->toString();
            $fragment = [
                "id" => $fragmentId,
                "multi" => false,
                "items" => [
                    [
                        "id" => Uuid::uuid4()->toString(),
                        "correct" => true,
                        "title" => trim($matches[2]),
                        "order" => 1,
                    ],
                ],
            ];
            $fragments[] = $fragment;
            return "{{$fragmentId}}";
        }, $content);

        try {
            $this->transactionManager->wrap(function() use ($content, $storyModel, $currentSlideModel, $fragments) {

                $nextSlideNumber = $currentSlideModel->number + 1;
                $title = $storyModel->title . " - Вопросы " . $nextSlideNumber;
                $testModel = StoryTest::create($title, $title, $title, "");
                $testModel->repeat = 1;
                if (!$testModel->save()) {
                    throw new \DomainException("Ошибка при создании теста");
                }

                $this->passTestService->create($testModel->id, new PassTestForm(null, [
                    "name" => "Выберите правильные ответы из вариантов, предложенных в списке",
                    "content" => $content,
                    "payload" => Json::encode([
                        "content" => $content,
                        "fragments" => $fragments,
                    ]),
                ]));

                $slideModel = $this->storySlideService->create($storyModel->id, 'New questions', StorySlide::KIND_QUESTION);
                $slideModel->number = $nextSlideNumber;
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
    }
}

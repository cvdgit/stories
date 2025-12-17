<?php

declare(strict_types=1);

namespace backend\controllers;

use backend\AiStoryAssist\MentalMapBuilder;
use backend\AiStoryAssist\StoryThread;
use backend\AiStoryAssist\ThreadResponse;
use backend\MentalMap\MentalMap;
use backend\MentalMap\MentalMapPayload;
use backend\MentalMap\MentalMapStorySlide;
use backend\Retelling\Retelling;
use backend\services\StoryEditorService;
use backend\services\StorySlideService;
use backend\SlideEditor\ContentMentalMap\SpeechTrainer;
use common\components\StoryCover;
use common\helpers\Translit;
use common\models\slide\SlideKind;
use common\models\Story;
use common\models\StorySlide;
use common\rbac\UserRoles;
use common\services\TransactionManager;
use DomainException;
use Exception;
use Ramsey\Uuid\Uuid;
use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\debug\Module;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;
use yii\web\View;
use yii\web\User as WebUser;

class StoryAiController extends Controller
{
    /**
     * @var StoryEditorService
     */
    private $storyEditorService;
    /**
     * @var StorySlideService
     */
    private $storySlideService;
    /**
     * @var TransactionManager
     */
    private $transactionManager;
    /**
     * @var MentalMapBuilder
     */
    private $mentalMapBuilder;

    public function __construct(
        $id,
        $module,
        StoryEditorService $storyEditorService,
        StorySlideService $storySlideService,
        TransactionManager $transactionManager,
        MentalMapBuilder $mentalMapBuilder,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->storyEditorService = $storyEditorService;
        $this->storySlideService = $storySlideService;
        $this->transactionManager = $transactionManager;
        $this->mentalMapBuilder = $mentalMapBuilder;
    }

    public function behaviors(): array
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

    public $layout = 'story-create-assist';

    public function actionCreate(string $id = null)
    {
        if ($id === null) {
            $threadId = Uuid::uuid4()->toString();
            return $this->redirect(['/story-ai/create', 'id' => $threadId]);
        }

        if (class_exists(Module::class)) {
            $this->view->off(View::EVENT_END_BODY, [Module::getInstance(), 'renderToolbar']);
        }

        return $this->render('create', [
            'threadId' => $id,
        ]);
    }

    public function actionCreateStoryHandler(Request $request, Response $response, WebUser $user): array
    {
        $response->format = Response::FORMAT_JSON;
        $payload = Json::decode($request->rawBody);

        $view = $payload['view'] ?? 'slide-full-text';
        $title = $payload['title'];
        $fragments = $payload['fragments'];
        $threadId = $payload['threadId'];

        $alias = Translit::translit($title);
        $foundStoryByAlias = Story::find()
            ->where(['title' => trim($title)])
            ->all();

        if (count($foundStoryByAlias) > 0) {
            $newTitle = $title . ' #' . (count($foundStoryByAlias) + 1);
            $alias = Translit::translit($newTitle);
        }

        $story = Story::create($title, $user->getId(), [Yii::$app->params['ai.story.assist.category.id']], $alias);
        $story->setIsAI();

        try {

            $storyResponse = [];

            $this->transactionManager->wrap(function() use ($story, $fragments, $threadId, $view, &$storyResponse): void {

                if (!$story->save()) {
                    throw new DomainException(
                        'Can\'t be saved Story model. Errors: ' . implode(', ', $story->getFirstErrors()),
                    );
                }

                $headerSlide = StorySlide::createSlide($story->id);
                $headerSlide->data = 'empty';
                $headerSlide->kind = SlideKind::SLIDE;
                if (!$headerSlide->save()) {
                    throw new DomainException(
                        'Can\'t be saved Story model. Errors: ' . implode(', ', $headerSlide->getFirstErrors()),
                    );
                }
                $headerSlide->data = $this->storyEditorService->makeSlideWithHeader($headerSlide->id, $story->title . ' ➡️');
                if (!$headerSlide->save()) {
                    throw new DomainException(
                        'Can\'t be saved Story model. Errors: ' . implode(', ', $headerSlide->getFirstErrors()),
                    );
                }

                $fragmentsToSlideMap = [];
                foreach ($fragments as $fragment) {
                    $slide = StorySlide::createSlide($story->id);
                    $slide->data = 'empty';
                    $slide->kind = SlideKind::SLIDE;
                    if (!$slide->save()) {
                        throw new DomainException(
                            'Can\'t be saved Story model. Errors: ' . implode(', ', $slide->getFirstErrors()),
                        );
                    }
                    $slide->data = $this->storyEditorService->makeSlideWithText($slide->id, nl2br($fragment['text']), $view);
                    if (!$slide->save()) {
                        throw new DomainException(
                            'Can\'t be saved Story model. Errors: ' . implode(', ', $slide->getFirstErrors()),
                        );
                    }

                    $fragmentsToSlideMap[] = [
                        'fragmentId' => $fragment['id'],
                        'slideId' => $slide->id,
                    ];
                }

                $slide = StorySlide::createSlide($story->id);
                $slide->data = $this->storyEditorService->makeEmptySlide();
                $slide->kind = SlideKind::SLIDE;
                if (!$slide->save()) {
                    throw new DomainException(
                        'Can\'t be saved Story model. Errors: ' . implode(', ', $slide->getFirstErrors()),
                    );
                }

                $thread = StoryThread::findOne($threadId);
                if ($thread !== null) {
                    $thread->setStory($story->id, $story->title);
                    $thread->save();
                }

                $storyResponse = [
                    'id' => $story->id,
                    'title' => $story->title,
                    'cover' => StoryCover::getListThumbPath($story->cover),
                    'viewUrl' => Yii::$app->urlManagerFrontend->createAbsoluteUrl(
                        ['/story/view', 'alias' => $story->alias],
                    ),
                    'editUrl' => Url::to(['/story/update', 'id' => $story->id]),
                    'slideMap' => $fragmentsToSlideMap,
                    'repetitionTrainer' => [
                        ['title' => 'Ментальная карта', 'type' => 'mental-map'],
                        ['title' => 'Ментальная карта (четные пропуски)', 'type' => 'mental-map-even-fragments'],
                        ['title' => 'Ментальная карта (нечетные пропуски)', 'type' => 'mental-map-odd-fragments'],
                        ['title' => 'Ментальная карта (план)', 'type' => 'mental-map-plan'],
                        ['title' => 'Пересказ', 'type' => 'retelling'],
                    ],
                ];
            });

            return [
                'success' => true,
                'story' => $storyResponse,
            ];
        } catch (Exception $exception) {
            Yii::$app->errorHandler->logException($exception);
            return ['success' => false, 'message' => $exception->getMessage()];
        }
    }

    /**
     * @throws Exception
     */
    public function actionCreateSlideContentHandler(Request $request, Response $response, WebUser $user): array
    {
        $response->format = Response::FORMAT_JSON;

        $payload = Json::decode($request->rawBody);
        $storyId = $payload['storyId'];
        $slideId = $payload['slideId'];
        $contents = $payload['contents'];
        $text = $payload['text'];

        $currentSlideModel = StorySlide::findOne($slideId);
        if ($currentSlideModel === null) {
            throw new NotFoundHttpException('Слайд не найден');
        }

        $textBlockIds = $this->storyEditorService->getTextBlockIds($currentSlideModel->data);
        if (count($textBlockIds) === 0) {
            throw new BadRequestHttpException('Text block not found');
        }

        try {
            $retellingSlideId = $this->createRetelling(
                $currentSlideModel->story_id,
                $currentSlideModel->id,
                $currentSlideModel->number,
                $user->getId(),
            );
        } catch (Exception $exception) {
            Yii::$app->errorHandler->logException($exception);
            throw new BadRequestHttpException($exception->getMessage());
        }

        $speechTrainer = SpeechTrainer::create(
            Uuid::uuid4(),
            'Речевой тренажёр',
            $currentSlideModel->id,
            $blockId = $textBlockIds[0],
            $retellingSlideId,
        );
        if (!$speechTrainer->save()) {
            throw new BadRequestHttpException('Speech Trainer save error');
        }

        foreach ($contents as $contentRow) {
            $this->transactionManager->wrap(function () use ($contentRow, $text, $user, $slideId, $blockId): void {
                $type = $contentRow['type'];
                if ($type === 'retelling') {
                    return;
                }

                $mentalMapId = Uuid::uuid4();

                if ($type === 'mental-map-plan') {
                    $payload = MentalMapPayload::planMentalMap(
                        $mentalMapId,
                        $contentRow['title'],
                        $text,
                        array_map(static function (array $fragment): array {
                            return [
                                'id' => $fragment['id'],
                                'title' => $fragment['title'],
                                'description' => $fragment['description'],
                            ];
                        }, MentalMapPayload::filterEmptyFragments($contentRow['fragments'])),
                        Uuid::fromString(Yii::$app->params['ai.story.assist.plan.prompt.id']),
                    );
                } else {
                    $payload = MentalMapPayload::treeMentalMap(
                        $mentalMapId,
                        $contentRow['title'],
                        $text, // preg_replace('/\<br(\s*)?\/?\>/i', "\n", $createForm->text),
                        array_map(static function (array $fragment): array {
                            return [
                                'id' => $fragment['id'],
                                'title' => $fragment['title'],
                            ];
                        }, MentalMapPayload::filterEmptyFragments($contentRow['fragments'])),
                    );
                }

                $mentalMap = MentalMap::create(
                    $mentalMapId->toString(),
                    $payload->getName(),
                    $payload->asArray(),
                    $user->getId(),
                    $type,
                );
                if (!$mentalMap->save()) {
                    throw new BadRequestHttpException('Mental Map save exception');
                }

                $command = Yii::$app->db->createCommand();
                $command->insert('mental_map_story_slide', [
                    'mental_map_id' => $mentalMap->uuid,
                    'slide_id' => $slideId,
                    'block_id' => $blockId,
                ]);
                $command->execute();
            });
        }

        return ['success' => true, 'slideId' => $slideId];
    }

    /**
     * @throws Exception
     */
    private function createRetelling(int $storyId, int $currentSlideId, int $currentSlideNumber, int $userId): int
    {
        $retelling = Retelling::create(
            Uuid::uuid4(),
            $currentSlideId,
            'Перескажите текст',
            '',
            false,
            $userId,
        );

        $retellingSlideId = null;
        $this->transactionManager->wrap(function() use ($retelling, $storyId, $currentSlideNumber, &$retellingSlideId): void {
            if (!$retelling->save()) {
                throw new DomainException('Retelling save error');
            }
            $retellingSlide = $this->storySlideService->createAndInsertSlide(
                $storyId,
                StorySlide::KIND_RETELLING,
                $currentSlideNumber,
                function (int $slideId) use ($retelling): string {
                    return $this->storyEditorService->getSlideWithRetellingBlockContent($slideId, $retelling->id);
                }
            );
            $retellingSlideId = $retellingSlide->id;
        });

        return $retellingSlideId;
    }

    public function actionThreads(Response $response, WebUser $user): array
    {
        $response->format = Response::FORMAT_JSON;
        return array_map(static function (StoryThread $thread): ThreadResponse {
            return ThreadResponse::fromModel($thread);
        }, StoryThread::findAllByUser($user->getId()));
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionState(string $id, Response $response, WebUser $user): ThreadResponse
    {
        $response->format = Response::FORMAT_JSON;
        $thread = StoryThread::findByUser($id, $user->getId());
        if ($thread === null) {
            throw new NotFoundHttpException('Thread not found');
        }
        return ThreadResponse::fromModel($thread);
    }

    /**
     * @throws BadRequestHttpException
     */
    public function actionCreateThread(Request $request, Response $response, WebUser $user): ThreadResponse
    {
        $response->format = Response::FORMAT_JSON;

        $payload = Json::decode($request->rawBody);
        $id = $payload['id'];
        $messages = $payload['messages'];
        $text = $payload['text'];

        $thread = StoryThread::findByUser($id, $user->getId());
        if ($thread === null) {
            $thread = StoryThread::create($id, 'Без имени', $user->getId(), $text, $messages);
            if (!$thread->save()) {
                throw new BadRequestHttpException('Story Thread save error');
            }
        } else {
            $thread->updateThread($messages);
            $thread->save();
        }

        return ThreadResponse::fromModel($thread);
    }

    public function actionSaveThread(Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $payload = Json::decode($request->rawBody);
        $id = $payload['id'];
        //$title = $payload['title'];
        $messages = $payload['messages'];
        $thread = StoryThread::findOne($id);
        $saved = false;
        if ($thread) {
            $thread->updateThread($messages);
            if ($thread->save()) {
                $saved = true;
            }
        }
        return ['success' => true, 'saved' => $saved, 'payload' => $payload];
    }

    /**
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function actionRemoveTrainer(Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $payload = Json::decode($request->rawBody);
        $storyId = (int) $payload['storyId'];
        if (empty($storyId)) {
            throw new BadRequestHttpException('Story not found');
        }
        $story = Story::findOne($storyId);
        if ($story === null) {
            throw new NotFoundHttpException('Story not found');
        }

        /** @var SpeechTrainer[] $trainers */
        $trainers = SpeechTrainer::find()
            ->where(['in', 'slide_id', $story->getSlideIDs()])
            ->all();

        foreach ($trainers as $trainer) {
            $this->transactionManager->wrap(function () use ($trainer): void {
                if ($trainer->retelling_slide_id !== null) {
                    $slideModel = StorySlide::findOne($trainer->retelling_slide_id);
                    if ($slideModel !== null) {
                        $this->storyEditorService->deleteSlide($slideModel);
                    }
                }
                $trainer->delete();

                $slideMentalMapIds = (new Query())
                    ->select('t.mental_map_id')
                    ->from(['t' => MentalMapStorySlide::tableName()])
                    ->where([
                        't.slide_id' => $trainer->slide_id,
                    ])
                    ->all();
                $slideMentalMapIds = array_column($slideMentalMapIds, 'mental_map_id');

                if (count($slideMentalMapIds) > 0) {
                    MentalMap::deleteAll(['in', 'uuid', $slideMentalMapIds]);
                }
            });
        }

        return ['success' => true];
    }

    /**
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    public function actionDeleteThread(Request $request, Response $response, WebUser $user): array
    {
        $response->format = Response::FORMAT_JSON;
        $payload = Json::decode($request->rawBody);
        $id = $payload['threadId'];
        $thread = StoryThread::findByUser($id, $user->getId());
        if ($thread === null) {
            throw new NotFoundHttpException('Thread not found');
        }
        try {
            if ($thread->delete() === false) {
                return ['success' => false, 'message' => 'Thread delete error'];
            }
            return ['success' => true];
        } catch (Exception $exception) {
            Yii::$app->errorHandler->logException($exception);
            return ['success' => false, 'message' => $exception->getMessage()];
        }
    }

    /**
     * @throws Exception
     */
    public function actionCreateSlideReadingHandler(Request $request, Response $response, WebUser $user): array
    {
        $response->format = Response::FORMAT_JSON;

        $payload = Json::decode($request->rawBody);
        $storyId = $payload['storyId'];
        $slideId = $payload['slideId'];
        $mentalMapContent = $payload['mentalMap'];
        $text = $payload['text'];

        $currentSlideModel = StorySlide::findOne($slideId);
        if ($currentSlideModel === null) {
            throw new NotFoundHttpException('Слайд не найден');
        }

        $this->transactionManager->wrap(function () use ($currentSlideModel, $mentalMapContent, $text, $user, $slideId): void {

            $slideModel = $this->storySlideService->create($currentSlideModel->story_id, 'empty', StorySlide::KIND_MENTAL_MAP);
            $slideModel->number = $currentSlideModel->number + 1;
            Story::insertSlideNumber($currentSlideModel->story_id, $currentSlideModel->number);
            if (!$slideModel->save()) {
                throw new DomainException(
                    'Can\'t be saved StorySlide model. Errors: ' . implode(', ', $slideModel->getFirstErrors()),
                );
            }

            $mentalMapId = Uuid::uuid4();
            $payload = MentalMapPayload::treeMentalMap(
                $mentalMapId,
                $mentalMapContent['title'],
                $text, // preg_replace('/\<br(\s*)?\/?\>/i', "\n", $createForm->text),
                array_map(static function (array $fragment): array {
                    return [
                        'id' => $fragment['id'],
                        'title' => $fragment['title'],
                    ];
                }, MentalMapPayload::filterEmptyFragments($mentalMapContent['fragments'])),
            );

            $mentalMap = MentalMap::create(
                $mentalMapId->toString(),
                $payload->getName(),
                $payload->asArray(),
                $user->getId(),
            );
            if (!$mentalMap->save()) {
                throw new BadRequestHttpException('Mental Map save exception');
            }

            $data = $this->storyEditorService->getSlideWithMentalMapBlockContent($slideModel->id, $mentalMapId->toString(), 'mental-map', false);
            $slideModel->updateData($data);
            if (!$slideModel->save()) {
                throw new DomainException(
                    'Can\'t be saved StorySlide model. Errors: ' . implode(', ', $slideModel->getFirstErrors()),
                );
            }

            $currentSlideModel->setHidden();
            if (!$currentSlideModel->save()) {
                throw new DomainException(
                    'Can\'t be saved StorySlide model. Errors: ' . implode(', ', $currentSlideModel->getFirstErrors()),
                );
            }
        });


        return ['success' => true, 'slideId' => $slideId];
    }

    /**
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function actionCreateFinalMentalMapHandler(Request $request, Response $response, WebUser $user): array
    {
        $response->format = Response::FORMAT_JSON;

        $payload = Json::decode($request->rawBody);
        $storyId = $payload['storyId'];
        $fragments = $payload['fragments'];

        $story = Story::findOne($storyId);
        if ($story === null) {
            throw new NotFoundHttpException('Story not found');
        }

        $lastSlide = $story->storySlides[count($story->storySlides) - 2];

        try {
            $this->transactionManager->wrap(function () use ($story, $lastSlide, $user, $fragments): void {
                $newTextSlide = $this->storySlideService->createAndInsertSlide(
                    $story->id,
                    SlideKind::SLIDE,
                    $lastSlide->number,
                    function (int $slideId): string {
                        return $this->storyEditorService->makeSlideWithHeader(
                            $slideId,
                            'Осталось пройти последний тест ➡️'
                        );
                    },
                );
                $this->mentalMapBuilder->createPlanMentalMap(
                    $mentalMapId = Uuid::uuid4(),
                    'Итоговая ментальная карта',
                    'text',
                    $user->getId(),
                    $fragments,
                    Uuid::fromString(Yii::$app->params['ai.story.assist.plan.prompt.id']),
                );
                $mapSlide = $this->storySlideService->createAndInsertSlide(
                    $story->id,
                    StorySlide::KIND_MENTAL_MAP,
                    $newTextSlide->number,
                    function (int $slideId) use ($mentalMapId): string {
                        return $this->storyEditorService->getSlideWithMentalMapBlockContent(
                            $slideId,
                            $mentalMapId->toString(),
                            'mental-map'
                        );
                    },
                );

                $allText = implode("\r\n", array_map(static function (array $fragment): string {
                    return $fragment['description'];
                }, $fragments));
                $newAllTextSlide = $this->storySlideService->createAndInsertSlide(
                    $story->id,
                    SlideKind::SLIDE,
                    $mapSlide->number,
                    function (int $slideId) use ($allText): string {
                        return $this->storyEditorService->makeSlideWithText(
                            $slideId,
                            $allText,
                            'all-text'
                        );
                    },
                    ['status' => StorySlide::STATUS_HIDDEN]
                );

                $this->createRetelling(
                    $story->id,
                    $newAllTextSlide->id,
                    $newAllTextSlide->number,
                    $user->getId()
                );
            });
            return ['success' => true];
        } catch (Exception $exception) {
            Yii::$app->errorHandler->logException($exception);
            return ['success' => false, 'message' => $exception->getMessage()];
        }
    }
}

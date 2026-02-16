<?php

declare(strict_types=1);

namespace backend\controllers;

use backend\AiStoryAssist\MentalMapBuilder;
use backend\AiStoryAssist\StoryThread;
use backend\AiStoryAssist\ThreadResponse;
use backend\components\story\TableOfContentsBlockContent;
use backend\MentalMap\MentalMap;
use backend\MentalMap\MentalMapStorySlide;
use backend\services\StoryEditorService;
use backend\services\StorySlideService;
use backend\SlideEditor\ContentMentalMap\SpeechTrainer;
use backend\StoryContent\SpeechTrainer\SpeechTrainerService;
use backend\TableOfContents\TableOfContentsGroup;
use backend\TableOfContents\TableOfContentsPayload;
use backend\TableOfContents\TableOfContentsService;
use common\components\StoryCover;
use common\helpers\Translit;
use common\models\slide\SlideKind;
use common\models\Story;
use common\models\StorySlide;
use common\rbac\UserRoles;
use common\services\TransactionManager;
use DomainException;
use Exception;
use modules\edu\components\ArrayHelper;
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
    /**
     * @var SpeechTrainerService
     */
    private $speechTrainerService;
    /**
     * @var TableOfContentsService
     */
    private $tableOfContentsService;

    public function __construct(
        $id,
        $module,
        StoryEditorService $storyEditorService,
        StorySlideService $storySlideService,
        TransactionManager $transactionManager,
        MentalMapBuilder $mentalMapBuilder,
        SpeechTrainerService $speechTrainerService,
        TableOfContentsService $tableOfContentsService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->storyEditorService = $storyEditorService;
        $this->storySlideService = $storySlideService;
        $this->transactionManager = $transactionManager;
        $this->mentalMapBuilder = $mentalMapBuilder;
        $this->speechTrainerService = $speechTrainerService;
        $this->tableOfContentsService = $tableOfContentsService;
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
        $contents = $payload['contents'] ?? null;
        $threadId = $payload['threadId'];

        $alias = Translit::translit($title);
        $foundStoryByAlias = Story::find()
            ->where(['alias' => trim($alias)])
            ->one();
        if ($foundStoryByAlias !== null) {
            $foundNewAlias = false;
            while (!$foundNewAlias) {
                $matches = [];
                if (preg_match('/-(\d+)$/', $alias, $matches)) {
                    $number = (int) $matches[1];
                    $number++;
                    $alias = preg_replace('/-(\d+)$/', '-' . $number, $alias);
                } else {
                    $alias .= '-2';
                }
                $foundStoryByAlias = Story::find()
                    ->where(['alias' => trim($alias)])
                    ->one();
                $foundNewAlias = $foundStoryByAlias === null;
            }
        }

        $story = Story::create(
            $title,
            $user->getId(),
            [Yii::$app->params['ai.story.assist.category.id']],
            $alias,
        );
        $story->setIsAI();

        try {

            $storyResponse = [];

            $this->transactionManager->wrap(function() use ($story, $fragments, $threadId, $view, &$storyResponse): void {

                if (!$story->save()) {
                    throw new DomainException(
                        'Can\'t be saved Story model. Errors: ' . implode(', ', $story->getFirstErrors()),
                    );
                }

                $this->storySlideService->createSlide(
                    $story->id,
                    SlideKind::SLIDE,
                    1,
                    function (int $slideId) use ($story): string {
                        return $this->storyEditorService->makeSlideWithHeader(
                            $slideId,
                            $story->title . ' ➡️',
                        );
                    },
                );

                $fragmentsToSlideMap = [];
                $number = 2;
                foreach ($fragments as $fragment) {

                    $slide = $this->storySlideService->createSlide(
                        $story->id,
                        SlideKind::SLIDE,
                        $number,
                        function (int $slideId) use ($fragment, $view): string {
                            return $this->storyEditorService->makeSlideWithText(
                                $slideId,
                                nl2br($fragment['text']),
                                $view,
                            );
                        },
                    );
                    $number++;

                    $fragmentsToSlideMap[] = [
                        'fragmentId' => $fragment['id'],
                        'slideId' => $slide->id,
                    ];
                }

                $this->storySlideService->createSlide(
                    $story->id,
                    SlideKind::SLIDE,
                    $number,
                    function (int $slideId): string {
                        return $this->storyEditorService->makeEmptySlide($slideId);
                    },
                );

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
                ];
            });

            if ($contents !== null) {
                $tableOfContentsPayload = new TableOfContentsPayload('Оглавление');
                foreach ($contents as $contentGroup) {
                    $group = new TableOfContentsGroup(Uuid::uuid4(), $contentGroup['name']);
                    foreach ($contentGroup['cards'] as $card) {
                        $group->addCard($cardId = Uuid::uuid4(), $card['name']);
                        foreach ($card['slides'] as $cardSlideId) {
                            $slideMap = ArrayHelper::array_find(
                                $storyResponse['slideMap'],
                                static function (array $row) use ($cardSlideId): bool {
                                    return $row['fragmentId'] === $cardSlideId;
                                },
                            );
                            if ($slideMap) {
                                $group->addSlide(
                                    $slideMap['slideId'],
                                    '',
                                    1,
                                    $cardId
                                );
                            }
                        }
                    }
                    $tableOfContentsPayload->addGroup($group);
                }

                $this->storySlideService->createAndInsertSlide(
                    $story->id,
                    SlideKind::SLIDE,
                    1,
                    function (int $slideId) use ($tableOfContentsPayload): string {
                        return $this->storyEditorService->makeSlideWithTableOfContents(
                            $slideId,
                            (new TableOfContentsBlockContent(
                                Uuid::uuid4()->toString(),
                                Json::encode($tableOfContentsPayload),
                            ))->render(),
                        );
                    },
                );
            }

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

        $story = Story::findOne($storyId);
        if ($story === null) {
            throw new NotFoundHttpException('История не найдена');
        }

        $tableOfContentsItems = $this->tableOfContentsService->getStoryTableOfContents($story->id);
        $tableOfContentsItem = null;
        if (count($tableOfContentsItems) > 0) {
            $tableOfContentsItem = $tableOfContentsItems[0];
        }

        $textBlockIds = $this->storyEditorService->getTextBlockIds($currentSlideModel->data);
        if (count($textBlockIds) === 0) {
            throw new BadRequestHttpException('Text block not found');
        }

        $retellingRow = ArrayHelper::array_find(
            $contents,
            static function (array $contentRow): bool {
                return $contentRow['type'] === 'retelling';
            },
        );

        $retellingSlideId = null;
        if ($retellingRow !== null) {
            try {
                $retellingSlideId = $this->speechTrainerService->createRetelling(
                    $currentSlideModel->story_id,
                    $currentSlideModel->id,
                    $currentSlideModel->number,
                    $user->getId(),
                    $retellingRow['required'],
                );
            } catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                throw new BadRequestHttpException($exception->getMessage());
            }
        }

        if ($retellingSlideId !== null && $tableOfContentsItem !== null) {
            $this->tableOfContentsService->updateTableOfContentsSlide(
                $tableOfContentsItem,
                $currentSlideModel->id,
                [$currentSlideModel->id, $retellingSlideId]
            );
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
                if ($type === 'mental-map-plan' || $type === 'mental-map-plan-accumulation') {
                    $this->mentalMapBuilder->createPlanMentalMap(
                        $mentalMapId,
                        $contentRow['title'],
                        $text,
                        $user->getId(),
                        $contentRow['fragments'],
                        Uuid::fromString(Yii::$app->params['ai.story.assist.plan.prompt.id']),
                        $type,
                    );
                } else {
                    $this->mentalMapBuilder->createTreeMentalMap(
                        $mentalMapId,
                        $contentRow['title'],
                        $text,
                        $user->getId(),
                        $contentRow['fragments'],
                        $type
                    );
                }

                $this->speechTrainerService->createMentalMapSlideRow(
                    $mentalMapId,
                    (int) $slideId,
                    $blockId,
                    (bool) $contentRow['required'],
                );
            });
        }

        return ['success' => true, 'slideId' => $slideId];
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
        $storyId = (int) $payload['storyId'];
        $slideId = (int) $payload['slideId'];
        $mentalMapContent = $payload['mentalMap'];
        $text = $payload['text'];

        $currentSlideModel = StorySlide::findOne($slideId);
        if ($currentSlideModel === null) {
            throw new NotFoundHttpException('Слайд не найден');
        }

        $story = Story::findOne($storyId);
        if ($story === null) {
            throw new NotFoundHttpException('История не найдена');
        }

        $tableOfContentsItems = $this->tableOfContentsService->getStoryTableOfContents($story->id);
        $tableOfContentsItem = null;
        if (count($tableOfContentsItems) > 0) {
            $tableOfContentsItem = $tableOfContentsItems[0];
        }

        $this->transactionManager->wrap(
            function () use ($currentSlideModel, $mentalMapContent, $text, $user, $tableOfContentsItem): void {
                $this->mentalMapBuilder->createTreeMentalMap(
                    $mentalMapId = Uuid::uuid4(),
                    $mentalMapContent['title'],
                    $text,
                    $user->getId(),
                    $mentalMapContent['fragments']
                );
                $mapSlide = $this->storySlideService->createAndInsertSlide(
                    $currentSlideModel->story_id,
                    StorySlide::KIND_MENTAL_MAP,
                    $currentSlideModel->number,
                    function (int $slideId) use ($mentalMapId): string {
                        return $this->storyEditorService->getSlideWithMentalMapBlockContent(
                            $slideId,
                            $mentalMapId->toString(),
                            'mental-map',
                            false,
                        );
                    },
                );
                $currentSlideModel->setHidden();
                if (!$currentSlideModel->save()) {
                    throw new DomainException(
                        'Can\'t be saved StorySlide model. Errors: ' . implode(
                            ', ',
                            $currentSlideModel->getFirstErrors(),
                        ),
                    );
                }

                if ($tableOfContentsItem !== null) {
                    $this->tableOfContentsService->updateTableOfContentsSlide(
                        $tableOfContentsItem,
                        $currentSlideModel->id,
                        [$mapSlide->id]
                    );
                }
            },
        );

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
                            'Осталось пройти последний тест ➡️',
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
                            'mental-map',
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
                            'all-text',
                        );
                    },
                    ['status' => StorySlide::STATUS_HIDDEN],
                );

                $this->speechTrainerService->createRetelling(
                    $story->id,
                    $newAllTextSlide->id,
                    $newAllTextSlide->number,
                    $user->getId(),
                    true,
                );
            });
            return ['success' => true];
        } catch (Exception $exception) {
            Yii::$app->errorHandler->logException($exception);
            return ['success' => false, 'message' => $exception->getMessage()];
        }
    }
}

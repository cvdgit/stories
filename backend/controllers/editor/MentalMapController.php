<?php

declare(strict_types=1);

namespace backend\controllers\editor;

use backend\components\BaseController;
use backend\components\story\AbstractBlock;
use backend\components\story\MentalMapBlock;
use backend\components\story\MentalMapBlockContent;
use backend\components\story\reader\HtmlSlideReader;
use backend\components\story\RetellingBlock;
use backend\components\story\RetellingBlockContent;
use backend\components\story\writer\HTMLWriter;
use backend\MentalMap\EditorCreateAi\CreateAiMentalMapsForm;
use backend\MentalMap\MentalMap;
use backend\MentalMap\MentalMapPayload;
use backend\MentalMap\MentalMapStorySlide;
use backend\models\editor\MentalMapForm;
use backend\Retelling\Retelling;
use backend\services\StoryEditorService;
use backend\services\StorySlideService;
use backend\SlideEditor\ContentMentalMap\ContentMentalMapForm;
use backend\SlideEditor\ContentMentalMap\SpeechTrainer;
use backend\SlideEditor\CreateMentalMapQuestions\UpdateMentalMapQuestionsForm;
use backend\StoryContent\SpeechTrainer\SpeechTrainerService;
use common\models\Story;
use common\models\StorySlide;
use common\rbac\UserRoles;
use common\services\TransactionManager;
use DomainException;
use Exception;
use modules\edu\components\ArrayHelper;
use Ramsey\Uuid\Uuid;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Query;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;
use yii\web\User as WebUser;

use function PHPUnit\Framework\containsEqual;

class MentalMapController extends BaseController
{
    /**
     * @var StoryEditorService
     */
    private $storyEditorService;
    /**
     * @var TransactionManager
     */
    private $transactionManager;
    /**
     * @var StorySlideService
     */
    private $storySlideService;
    /**
     * @var SpeechTrainerService
     */
    private $speechTrainerService;

    public function __construct(
        $id,
        $controller,
        StoryEditorService $editorService,
        TransactionManager $transactionManager,
        StorySlideService $storySlideService,
        SpeechTrainerService $speechTrainerService,
        $config = []
    ) {
        parent::__construct($id, $controller, $config);
        $this->storyEditorService = $editorService;
        $this->transactionManager = $transactionManager;
        $this->storySlideService = $storySlideService;
        $this->speechTrainerService = $speechTrainerService;
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::PERMISSION_MANAGE_TEST],
                    ],
                ],
            ],
        ];
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function actionFragments(string $id, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $mentalMap = $this->findModel(MentalMap::class, $id);
        return [
            'success' => true,
            'items' => array_map(static function (array $item): array {
                return ['id' => $item['id'], 'text' => $item['text'] ?? $item['title']];
            }, $mentalMap->getItems()),
        ];
    }

    /**
     * @throws NotFoundHttpException
     * @throws InvalidConfigException
     */
    public function actionSourceFragments(int $slide_id, string $block_id, string $mental_map_id, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;

        $slideModel = $this->findModel(StorySlide::class, $slide_id);

        $slide = (new HtmlSlideReader($slideModel->getSlideOrLinkData()))->load();
        /** @var MentalMapBlock $block */
        $block = $slide->findBlockByID($block_id);
        $content = MentalMapBlockContent::createFromHtml($block->getContent());

        $mentalMap = $this->findModel(MentalMap::class, $mental_map_id);
        $sourceMentalMap = $this->findModel(MentalMap::class, $mentalMap->source_mental_map_id);
        return [
            'success' => true,
            'items' => array_map(static function (array $item): array {
                return ['id' => $item['id'], 'text' => $item['text'] ?? $item['title']];
            }, $sourceMentalMap->getItems()),
            'questions' => $mentalMap->getQuestions(),
            'required' => $content->isRequired(),
        ];
    }

    public function actionUpdateQuestions(Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;

        $payload = Json::decode($request->rawBody);

        $updateForm = new UpdateMentalMapQuestionsForm();
        if ($updateForm->load($payload, '')) {
            if (!$updateForm->validate()) {
                return ['success' => false, 'message' => 'Not valid'];
            }

            $mentalMap = MentalMap::findOne($updateForm->mentalMapId);
            if ($mentalMap === null) {
                return ['success' => 'false', 'message' => 'Mental map not found'];
            }

            $updateMentalMapForm = new MentalMapForm();
            $updateMentalMapForm->slide_id = $updateForm->slideId;
            $updateMentalMapForm->block_id = $updateForm->blockId;
            $updateMentalMapForm->mental_map_id = $updateForm->mentalMapId;
            $updateMentalMapForm->required = $updateForm->required ? '1' : '0';

            try {
                $mentalMap->updateQuestions($updateForm->fragments);
                if (!$mentalMap->save()) {
                    throw new DomainException('MentalMap update error');
                }
                $html = $this->storyEditorService->updateMentalMapBlock($updateMentalMapForm);
                return ['success' => true, 'block_id' => $updateForm->blockId, 'html' => $html];
            } catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                return ['success' => false, 'message' => $exception->getMessage()];
            }
        }

        return ['success' => false];
    }

    public function actionCreateAiForm(int $slide_id): string
    {
        $mentalMapForm = new CreateAiMentalMapsForm();
        return $this->renderAjax('_create_ai', [
            'formModel' => $mentalMapForm,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionCreateAiHandler(Request $request, Response $response, WebUser $user): array
    {
        $response->format = Response::FORMAT_JSON;
        $mentalMapForm = new CreateAiMentalMapsForm();
        if ($mentalMapForm->load($request->post(), '')) {

            if (!$mentalMapForm->validate()) {
                return ['success' => false, 'message' => 'Not valid'];
            }

            $currentSlideModel = StorySlide::findOne($mentalMapForm->currentSlideId);
            if ($currentSlideModel === null) {
                throw new NotFoundHttpException('Слайд не найден');
            }

            $storyModel = $currentSlideModel->story;

            $mentalMaps = Json::decode($mentalMapForm->mentalMaps);
            $newSlideId = null;
            foreach ($mentalMaps as $mentalMapRow) {
                if ($newSlideId !== null) {
                    $currentSlideModel = StorySlide::findOne($newSlideId);
                    if ($currentSlideModel === null) {
                        throw new NotFoundHttpException('Слайд не найден');
                    }
                }
                try {
                    $this->transactionManager->wrap(function () use ($mentalMapForm, &$newSlideId, $storyModel, $currentSlideModel, $user, $mentalMapRow) {

                        $slideModel = $this->storySlideService->create($storyModel->id, 'empty', StorySlide::KIND_MENTAL_MAP);
                        $slideModel->number = $currentSlideModel->number + 1;
                        Story::insertSlideNumber($storyModel->id, $currentSlideModel->number);
                        if (!$slideModel->save()) {
                            throw new DomainException(
                                'Can\'t be saved StorySlide model. Errors: ' . implode(', ', $slideModel->getFirstErrors()),
                            );
                        }

                        $mentalMapId = Uuid::uuid4()->toString();

                        $payload = [
                            'id' => $mentalMapId,
                            'name' => $mentalMapRow['title'],
                            'text' => $mentalMapForm->text,
                            'treeView' => true,
                            'map' => [
                                'url' => '/img/mental_map_blank.jpg',
                                'width' => 1080,
                                'height' => 720,
                                'images' => [],
                            ],
                            'mapTypeIsMentalMapQuestions' => false,
                            'treeData' => array_map(static function(string $textFragment): array {
                                return [
                                    'id' => Uuid::uuid4()->toString(),
                                    'title' => $textFragment,
                                ];
                            }, $mentalMapRow['fragments']),
                        ];

                        $mentalMap = MentalMap::create($mentalMapId, $mentalMapRow['title'], $payload, $user->getId());
                        if (!$mentalMap->save()) {
                            throw new BadRequestHttpException('Mental Map save exception');
                        }

                        $data = $this->storyEditorService->getSlideWithMentalMapBlockContent($slideModel->id, $mentalMapId, 'mental-map', false);
                        $slideModel->updateData($data);
                        if (!$slideModel->save()) {
                            throw new DomainException(
                                'Can\'t be saved StorySlide model. Errors: ' . implode(', ', $slideModel->getFirstErrors()),
                            );
                        }
                        $newSlideId = $slideModel->id;
                    });
                } catch (Exception $exception) {
                    Yii::$app->errorHandler->logException($exception);
                    return ["success" => false, "message" => $exception->getMessage()];
                }
            }

            return ["success" => true, 'slide_id' => $newSlideId];
        }
        return ['success' => false];
    }

    /**
     * @throws Exception
     */
    public function actionCreateContentHandler(Request $request, Response $response, WebUser $user): array
    {
        $response->format = Response::FORMAT_JSON;

        $createForm = new ContentMentalMapForm();
        if ($createForm->load($request->post(), '')) {
            if (!$createForm->validate()) {
                return ['success' => false, 'message' => 'Not valid'];
            }

            $mentalMaps = Json::decode($createForm->mentalMaps);

            $currentSlideModel = StorySlide::findOne($createForm->slideId);
            if ($currentSlideModel === null) {
                throw new NotFoundHttpException('Слайд не найден');
            }

            $retellingRow = ArrayHelper::array_find(
                $mentalMaps,
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
                        (bool) $retellingRow['required']
                    );
                } catch (Exception $exception) {
                    Yii::$app->errorHandler->logException($exception);
                    throw new BadRequestHttpException($exception->getMessage());
                }
            }

            $speechTrainers = SpeechTrainer::findAllBySlide($currentSlideModel->id);
            if (count($speechTrainers) > 0) {
                $speechTrainer = $speechTrainers[0];
                $speechTrainer->setRetellingSlideId($retellingSlideId);
            } else {
                $speechTrainer = SpeechTrainer::create(
                    Uuid::uuid4(),
                    'Речевой тренажёр',
                    $currentSlideModel->id,
                    $createForm->blockId,
                    $retellingSlideId,
                );
            }
            if (!$speechTrainer->save()) {
                throw new BadRequestHttpException('Speech Trainer save error');
            }

            foreach ($mentalMaps as $mentalMapRow) {

                $this->transactionManager->wrap(function() use ($mentalMapRow, $createForm, $user): void {

                    $type = $mentalMapRow['type'];
                    if ($type === 'retelling') {
                        return;
                    }

                    $mentalMapId = Uuid::uuid4();
                    $payload = MentalMapPayload::treeMentalMap(
                        $mentalMapId,
                        $mentalMapRow['title'],
                        preg_replace('/\<br(\s*)?\/?\>/i', "\n", $createForm->text),
                        array_map(static function(array $fragment): array {
                            return [
                                'id' => $fragment['id'],
                                'title' => $fragment['title'],
                            ];
                        }, MentalMapPayload::filterEmptyFragments($mentalMapRow['fragments'])),
                    );

                    $mentalMap = MentalMap::create(
                        $mentalMapId->toString(),
                        $payload->getName(),
                        $payload->asArray(),
                        $user->getId(),
                        $mentalMapRow['type'],
                    );
                    if (!$mentalMap->save()) {
                        throw new BadRequestHttpException('Mental Map save exception');
                    }

                    $this->speechTrainerService->createMentalMapSlideRow(
                        Uuid::fromString($mentalMap->uuid),
                        (int) $createForm->slideId,
                        $createForm->blockId,
                        (bool) $mentalMapRow['required'],
                    );
                });
            }

            return ['success' => true];
        }

        return ['success' => false];
    }

    public function actionContentForm(int $slide_id, string $block_id): string
    {
        $speechTrainer = SpeechTrainer::findOne([
            'slide_id' => $slide_id,
            'block_id' => $block_id,
        ]);
        $contents = [];
        if ($speechTrainer !== null) {
            $contents = $speechTrainer->getContents();
        }
        $mapOrder = SpeechTrainer::getAllTypes();
        return $this->renderAjax('_content', [
            'contents' => $contents,
            'mapOrder' => array_map(
                static function (string $type) use ($mapOrder): array {
                    return [
                        'type' => $type,
                        'title' => $mapOrder[$type] ?? 'Unknown type',
                    ];
                },
                array_keys($mapOrder),
            ),
        ]);
    }

    /**
     * @throws BadRequestHttpException
     */
    public function actionUpdateContentHandler(Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;

        $contentForm = new ContentMentalMapForm();
        if ($contentForm->load($request->post(), '')) {
            if (!$contentForm->validate()) {
                return ['success' => false, 'message' => 'Not valid'];
            }

            $mentalMaps = Json::decode($contentForm->mentalMaps);

            foreach ($mentalMaps as $mentalMapRow) {

                $type = $mentalMapRow['type'];
                if ($type === 'retelling') {
                    continue;
                }

                $mentalMapId = $mentalMapRow['id'];
                $mentalMap = MentalMap::findOne($mentalMapId);
                if ($mentalMap === null) {
                    throw new BadRequestHttpException('Mental map not found');
                }

                $mentalMap->name = $mentalMapRow['title'];
                $mentalMap->updateMapText(preg_replace('/\<br(\s*)?\/?\>/i', "\n", $contentForm->text));
                $mentalMap->updateTreeData(array_map(static function(array $fragment): array {
                    return [
                        'id' => $fragment['id'],
                        'title' => $fragment['title'],
                    ];
                }, $mentalMapRow['fragments']));

                if (!$mentalMap->save()) {
                    throw new BadRequestHttpException('Mental Map save exception');
                }
            }

            return ['success' => true];
        }

        return ['success' => false];
    }

    /**
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete(int $slide_id, string $block_id, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;

        $speechTrainer = SpeechTrainer::findOne([
            'slide_id' => $slide_id,
            'block_id' => $block_id,
        ]);

        if ($speechTrainer !== null) {
            if ($speechTrainer->retelling_slide_id !== null) {
                $slideModel = StorySlide::findOne($speechTrainer->retelling_slide_id);
                if ($slideModel !== null) {
                    $this->storyEditorService->deleteSlide($slideModel);
                }
            }
            $speechTrainer->delete();
        }

        $slideMentalMapIds = (new Query())
            ->select('t.mental_map_id')
            ->from(['t' => MentalMapStorySlide::tableName()])
            ->where([
                't.slide_id' => $slide_id,
                't.block_id' => $block_id,
            ])
            ->all();
        $slideMentalMapIds = array_column($slideMentalMapIds, 'mental_map_id');

        if (count($slideMentalMapIds) > 0) {
            MentalMap::deleteAll(['in', 'uuid', $slideMentalMapIds]);
        }

        return ['success' => true];
    }

    /**
     * @throws NotFoundHttpException
     * @throws BadRequestHttpException
     */
    public function actionContentRequired(int $slide_id, string $id, string $type, Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;

        if (!SpeechTrainer::isValidType($type)) {
            throw new BadRequestHttpException('Unknown type');
        }

        $payload = Json::decode($request->rawBody);
        $required = $payload['required'];

        $storySlide = StorySlide::findOne($slide_id);
        if ($storySlide === null) {
            throw new NotFoundHttpException('Slide not found');
        }

        if ($type === SpeechTrainer::TYPE_RETELLING) {

            $retelling = Retelling::findOne($id);
            if ($retelling === null) {
                throw new NotFoundHttpException('Retelling not found');
            }

            $speechTrainers = SpeechTrainer::findAllBySlide($storySlide->id);
            foreach ($speechTrainers as $speechTrainer) {

                if ($speechTrainer->retelling_slide_id === null) {
                    continue;
                }

                $retellingSlide = StorySlide::findOne($speechTrainer->retelling_slide_id);
                if ($retellingSlide === null) {
                    continue;
                }

                $slide = (new HtmlSlideReader($retellingSlide->getSlideOrLinkData()))->load();
                /** @var RetellingBlock|null $retellingBlock */
                $retellingBlock = null;
                foreach ($slide->getBlocks() as $slideBlock) {
                    if ($slideBlock->typeIs(AbstractBlock::TYPE_RETELLING)) {
                        $retellingBlock = $slideBlock;
                    }
                }
                if ($retellingBlock === null) {
                    continue;
                }

                $retellingContent = RetellingBlockContent::createFromHtml($retellingBlock->getContent());
                if ($retellingContent->getId() !== $retelling->id) {
                    continue;
                }

                $retellingBlock->setContent(
                    (new RetellingBlockContent($retelling->id, $required))->render()
                );

                $retellingSlide->data = (new HTMLWriter())->renderSlide($slide);
                if (!$retellingSlide->save()) {
                    throw new BadRequestHttpException('Retelling save error');
                }
            }

            return ['success' => true];
        }

        $mentalMap = MentalMap::findOne($id);
        if ($mentalMap === null) {
            throw new NotFoundHttpException('Mental map not found');
        }

        $mentalMapRow = MentalMapStorySlide::findMentalMapRow($storySlide->id, $mentalMap->uuid);
        if ($mentalMapRow === null) {
            throw new NotFoundHttpException('Mental Map Story Slide not found');
        }

        $mentalMapRow->setRequired($required);
        if (!$mentalMapRow->save()) {
            throw new BadRequestHttpException('Save error');
        }

        return ['success' => true];
    }
}

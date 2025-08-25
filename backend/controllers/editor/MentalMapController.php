<?php

declare(strict_types=1);

namespace backend\controllers\editor;

use backend\components\BaseController;
use backend\components\story\MentalMapBlock;
use backend\components\story\MentalMapBlockContent;
use backend\components\story\reader\HtmlSlideReader;
use backend\MentalMap\EditorCreateAi\CreateAiMentalMapsForm;
use backend\MentalMap\MentalMap;
use backend\models\editor\MentalMapForm;
use backend\services\StoryEditorService;
use backend\services\StorySlideService;
use backend\SlideEditor\CreateMentalMapQuestions\UpdateMentalMapQuestionsForm;
use common\models\Story;
use common\models\StorySlide;
use common\rbac\UserRoles;
use common\services\TransactionManager;
use DomainException;
use Exception;
use Ramsey\Uuid\Uuid;
use Yii;
use yii\base\InvalidConfigException;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;
use yii\web\User as WebUser;

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

    public function __construct(
        $id,
        $controller,
        StoryEditorService $editorService,
        TransactionManager $transactionManager,
        StorySlideService $storySlideService,
        $config = []
    ) {
        parent::__construct($id, $controller, $config);
        $this->storyEditorService = $editorService;
        $this->transactionManager = $transactionManager;
        $this->storySlideService = $storySlideService;
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
}

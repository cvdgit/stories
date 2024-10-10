<?php

namespace backend\controllers;

use backend\components\BaseController;
use backend\components\editor\EditorConfig;
use backend\components\editor\SlideListResponse;
use backend\components\SlideModifier;
use backend\components\story\AbstractBlock;
use backend\components\story\reader\HtmlSlideReader;
use backend\components\story\TextBlock;
use backend\models\editor\ButtonForm;
use backend\models\editor\ImageForm;
use backend\models\editor\MentalMapForm;
use backend\models\editor\QuestionForm;
use backend\models\editor\SlideLinkForm;
use backend\models\editor\SlideSourceForm;
use backend\models\editor\TestForm;
use backend\models\editor\TextForm;
use backend\models\editor\TransitionForm;
use backend\models\editor\VideoForm;
use backend\models\video\VideoSource;
use backend\services\StoryLinksService;
use backend\services\StorySlideService;
use backend\SlideEditor\CreateMentalMap\CreateMentalMapAction;
use backend\SlideEditor\CreatePassTest\CreatePassTestAction;
use backend\SlideEditor\CreateQuizBySlideText\CreateQuizAction;
use common\models\Lesson;
use common\models\StorySlide;
use common\services\TransactionManager;
use DomainException;
use Exception;
use Yii;
use Yii\base\InvalidConfigException;
use yii\db\Query;
use yii\filters\AccessControl;
use common\models\Story;
use common\services\StoryService;
use common\rbac\UserRoles;
use backend\services\StoryEditorService;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;

class EditorController extends BaseController
{
    protected $storyService;
    protected $editorService;
    private $storyLinksService;
    /**
     * @var TransactionManager
     */
    private $transactionManager;
    /**
     * @var StoryEditorService
     */
    private $storyEditorService;
    /**
     * @var StorySlideService
     */
    private $storySlideService;

    public function __construct(
        $id,
        $module,
        StoryService $storyService,
        StoryEditorService $editorService,
        StoryLinksService $storyLinksService,
        TransactionManager $transactionManager,
        StorySlideService $storySlideService,
        StoryEditorService $storyEditorService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->storyService = $storyService;
        $this->editorService = $editorService;
        $this->storyLinksService = $storyLinksService;
        $this->transactionManager = $transactionManager;
        $this->storySlideService = $storySlideService;
        $this->storyEditorService = $storyEditorService;
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::PERMISSION_EDITOR_ACCESS],
                    ],
                ],
            ],
        ];
    }

    public function actions(): array
    {
        return [
            "gpt-quiz" => CreateQuizAction::class,
            "create-pass-test" => CreatePassTestAction::class,
            'mental-map' => CreateMentalMapAction::class,
        ];
    }

    private function getEditorConfig(Story $story): EditorConfig
    {
        $storyID = $story->id;
        return (new EditorConfig())
            ->setValue('storyID', $storyID)
            ->setValue('lessonID')
            ->setUrlValues([
                'slidesEndpoint' => ['editor/slides', 'story_id' => $storyID],
                'getSlideAction' => ['/editor/load-slide', 'story_id' => $storyID],
                'getSlideBlocksAction' => ['/editor/slide-blocks'],
                'getBlockFormAction' => ['/editor/form'],
                'createBlockAction' => ['/editor/create-block'],
                'newCreateBlockAction' => ['editor/block/create'],
                'deleteBlockAction' => ['/editor/delete-block'],
                'deleteSlideAction' => ['editor/delete-slide'],
                'currentSlidesAction' => ['editor/slides', 'story_id' => $storyID],
                'slideVisibleAction' => ['editor/slide-visible'],
                'createSlideAction' => ['editor/create-slide', 'story_id' => $storyID],
                'createSlideLinkAction' => ['editor/create-slide-link', 'story_id' => $storyID],
                'slidesAction' => ['editor/slides'],
                'createSlideQuestionAction' => ['editor/create-slide-question', 'story_id' => $storyID],
                'createNewSlideQuestionAction' => ['editor/new-create-slide-question'],
                'copySlideAction' => ['editor/copy-slide'],
                'storyImagesAction' => ['editor/image/list'],
            ])
            ->setValue(
                'storyUrl',
                Yii::$app->urlManagerFrontend->createAbsoluteUrl(['story/view', 'alias' => $story->alias]),
            );
    }

    public function actionEdit($id)
    {
        $model = $this->findModel(Story::class, $id);
        $this->layout = 'editor';
        return $this->render('edit', [
            'model' => $model,
            'configJSON' => $this->getEditorConfig($model)->asJson(),
        ]);
    }

    public function actionLesson(string $uuid): string
    {
        $this->layout = 'editor';

        if (($lessonModel = Lesson::findOneByUUID($uuid)) === null) {
            throw new NotFoundHttpException('Lesson not found');
        }

        $storyModel = $lessonModel->story;
        $config = $this->getEditorConfig($storyModel)
            ->setValue('lessonID', $lessonModel->id)
            ->setUrlValue('slidesEndpoint', ['lesson/slides', 'id' => $lessonModel->id]);

        return $this->render('lesson', [
            'model' => $storyModel,
            'configJSON' => $config->asJson(),
            'lesson' => $lessonModel,
        ]);
    }

    public function actionLoadSlide(int $story_id, int $slide_id = -1)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($slide_id === -1) {
            $model = StorySlide::findFirstSlide($story_id);
        } else {
            $model = StorySlide::findSlide($slide_id);
        }
        if ($model->isLink()) {
            $linkSlide = StorySlide::findSlideByID($model->link_slide_id);
            $model->data = $linkSlide->data;
        }
        $slideData = (new SlideModifier($model->id, $model->data))
            ->addImageId()
            ->addImageParams()
            ->addDescription()
            ->render();
        return [
            'id' => $model->id,
            'status' => $model->status,
            'data' => $slideData,
            'haveLinks' => (count($model->storySlideBlocks) > 0),
            'number' => $model->number,
            'haveNeoRelations' => (count($model->neoSlideRelations) > 0),
        ];
    }

    private $types = [
        AbstractBlock::TYPE_HEADER => [
            'class' => TextForm::class,
            'view' => '_text_form',
        ],
        AbstractBlock::TYPE_TEXT => [
            'class' => TextForm::class,
            'view' => '_text_form',
        ],
        AbstractBlock::TYPE_IMAGE => [
            'class' => ImageForm::class,
            'view' => '_image_form',
        ],
        AbstractBlock::TYPE_BUTTON => [
            'class' => ButtonForm::class,
            'view' => '_button_form',
        ],
        AbstractBlock::TYPE_TRANSITION => [
            'class' => TransitionForm::class,
            'view' => '_transition_form',
        ],
        AbstractBlock::TYPE_TEST => [
            'class' => TestForm::class,
            'view' => '_test_form',
        ],
        AbstractBlock::TYPE_HTML => [
            'class' => QuestionForm::class,
            'view' => '_html_form',
        ],
        AbstractBlock::TYPE_VIDEO => [
            'class' => VideoForm::class,
            'view' => '_video_form',
            'source' => VideoSource::YOUTUBE,
        ],
        AbstractBlock::TYPE_VIDEOFILE => [
            'class' => VideoForm::class,
            'view' => '_video_form',
            'source' => VideoSource::FILE,
        ],
        AbstractBlock::TYPE_MENTAL_MAP => [
            'class' => MentalMapForm::class,
            'view' => '_mental_map_form',
        ],
    ];

    /**
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function actionFormCreate(int $slide_id, string $block_type)
    {
        /** @var StorySlide $model */
        $model = $this->findModel(StorySlide::class, $slide_id);
        if (!isset($this->types[$block_type])) {
            throw new DomainException($block_type . ' - Unknown block type');
        }
        $form = Yii::createObject($this->types[$block_type]);
        $form->slide_id = $model->id;
        $form->story_id = $model->story_id;
        return $this->renderAjax('create', [
            'model' => $form,
            'action' => ['editor/create-block/' . $block_type],
            'widgetStoryModel' => $model->story,
        ]);
    }

    public function actionForm(int $slide_id, string $block_id)
    {
        /** @var StorySlide $slideModel */
        $slideModel = $this->findModel(StorySlide::class, $slide_id);

        $slide = (new HtmlSlideReader($slideModel->data))->load();
        $block = $slide->findBlockByID($block_id);
        $block_type = $block->getType();
        if (!isset($this->types[$block_type])) {
            throw new DomainException($block_type . ' - Unknown block type');
        }

        $form = Yii::createObject($this->types[$block_type]);
        $form->slide_id = $slideModel->id;
        $form->block_id = $block_id;
        $form->story_id = $slideModel->story_id;

        $values = $block->getValues();
        $form->load($values, '');

        $widgetStoryModel = $slideModel->story;

        if ($block->getType() === AbstractBlock::TYPE_TRANSITION && $form->transition_story_id === null) {
            $form->transition_story_id = $slideModel->story_id;
            $widgetStoryModel = $this->findModel(Story::class, $form->transition_story_id);
        }

        if ($block->getType() === AbstractBlock::TYPE_IMAGE && $form->actionStoryID !== null) {
            $widgetStoryModel = $this->findModel(Story::class, $form->actionStoryID);
        }

        $view = ($block->getType() === AbstractBlock::TYPE_VIDEO || $block->getType(
            ) === AbstractBlock::TYPE_VIDEOFILE) ? 'update_video' : 'update';
        return $this->renderAjax($view, [
            'model' => $form,
            'action' => ['editor/update-block/' . str_replace('_', '-', $block_type)],
            'widgetStoryModel' => $widgetStoryModel,
        ]);
    }

    public function actionCreateSlideLink()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $form = new SlideLinkForm();
        $response = ['success' => false];
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $response['id'] = $this->editorService->createSlideLink(
                    $form->story_id,
                    $form->link_slide_id,
                    $form->slide_id,
                );
                $response['success'] = true;
            } catch (Exception $ex) {
                $response['error'] = $ex->getMessage();
            }
        }
        return $response;
    }

    /**
     * @throws NotFoundHttpException
     * @throws InvalidConfigException
     */
    public function actionSlides(int $story_id, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $model = $this->findModel(Story::class, $story_id);
        return array_map(static function (StorySlide $slide): array {
            return (new SlideListResponse($slide))->asArray();
        }, $model->storySlides);
    }

    public function actionSaveSlideSource()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new SlideSourceForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $slideModel = $this->findModel(StorySlide::class, $model->slide_id);
            $model->saveSlideSource($slideModel);
            return ['success' => true, 'id' => $slideModel->id];
        }
        return ['success' => false];
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionImportFromText(int $story_id, int $current_slide_id, Request $request, Response $response)
    {
        $response->format = Response::FORMAT_JSON;

        $storyModel = Story::findOne($story_id);
        if ($storyModel === null) {
            return ['success' => false, 'message' => 'История не найдена'];
        }

        $currentSlideModel = StorySlide::findOne($current_slide_id);
        if ($currentSlideModel === null) {
            return ['success' => false, 'message' => 'Слайд не найден'];
        }

        $jsonBody = Json::decode($request->rawBody, false);
        $texts = $jsonBody->texts;

        $newSlideId = null;
        try {
            $this->transactionManager->wrap(
                function () use ($texts, &$newSlideId, $storyModel, $currentSlideModel) {

                    $slides = (new Query())
                        ->from('{{%story_slide}}')
                        ->select(['id', 'number'])
                        ->where('story_id = :story', [':story' => $storyModel->id])
                        ->orderBy(['number' => SORT_ASC])
                        ->all();

                    $index = 1;
                    foreach ($texts as $text) {
                        $slideModel = $this->storySlideService->create(
                            $storyModel->id,
                            'empty',
                            StorySlide::KIND_SLIDE,
                        );
                        $slideModel->number = $currentSlideModel->number + $index;
                        //Story::insertSlideNumber($storyModel->id, $currentSlideModel->number);
                        if (!$slideModel->save()) {
                            throw new DomainException(
                                'Can\'t be saved StorySlide model. Errors: ' . implode(', ', $slideModel->getFirstErrors()),
                            );
                        }

                        $textBlock = new TextBlock();
                        $textBlock->setType(AbstractBlock::TYPE_TEXT);
                        $textBlock->setText(nl2br($text));
                        $textBlock->setLeft('20px');
                        $textBlock->setTop('20px');
                        $textBlock->setWidth('1200px');
                        $textBlock->setHeight('auto');

                        $slideModel->updateData($this->storyEditorService->renderBlock($slideModel->data, $textBlock));
                        if (!$slideModel->save()) {
                            throw new DomainException(
                                'Can\'t be saved StorySlide model. Errors: ' . implode(', ', $slideModel->getFirstErrors()),
                            );
                        }

                        if ($newSlideId === null) {
                            $newSlideId = $slideModel->id;
                        }

                        $index++;
                    }

                    $command = Yii::$app->db->createCommand();
                    $next = $currentSlideModel->number + count($texts) + 1;
                    foreach ($slides as $slide) {
                        if ($slide['number'] > $currentSlideModel->number) {
                            $command->update('{{%story_slide}}', ['number' => $next], ['id' => $slide['id']]);
                            $command->execute();
                            $next++;
                        }
                    }
                },
            );

            return ["success" => true, 'slide_id' => $newSlideId];
        } catch (Exception $exception) {
            Yii::$app->errorHandler->logException($exception);
            return ["success" => false, "message" => $exception->getMessage()];
        }
    }
}

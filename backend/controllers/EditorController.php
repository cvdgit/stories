<?php

namespace backend\controllers;

use backend\components\BaseController;
use backend\components\SlideModifier;
use backend\components\story\AbstractBlock;
use backend\components\story\ButtonBlock;
use backend\components\story\ImageBlock;
use backend\components\story\reader\HtmlSlideReader;
use backend\components\story\TestBlock;
use backend\components\story\TextBlock;
use backend\components\story\TransitionBlock;
use backend\components\story\VideoBlock;
use backend\components\story\VideoFileBlock;
use backend\components\story\writer\HTMLWriter;
use backend\models\editor\ButtonForm;
use backend\models\editor\ImageForm;
use backend\models\editor\QuestionForm;
use backend\models\editor\SlideLinkForm;
use backend\models\editor\SlideSourceForm;
use backend\models\editor\TestForm;
use backend\models\editor\TextForm;
use backend\models\editor\TransitionForm;
use backend\models\editor\VideoForm;
use backend\models\video\VideoSource;
use backend\services\StoryLinksService;
use common\models\StorySlide;
use common\models\StoryTest;
use DomainException;
use Exception;
use Yii;
use yii\filters\AccessControl;
use common\models\Story;
use common\services\StoryService;
use common\rbac\UserRoles;
use backend\services\StoryEditorService;
use yii\web\Response;

class EditorController extends BaseController
{

    protected $storyService;
    protected $editorService;
    private $storyLinksService;

    public function __construct($id, $module, StoryService $storyService, StoryEditorService $editorService, StoryLinksService $storyLinksService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->storyService = $storyService;
        $this->editorService = $editorService;
        $this->storyLinksService = $storyLinksService;
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

	public function actionEdit($id)
	{
        $model = $this->findModel(Story::class, $id);
	    $this->layout = 'editor';
        return $this->render('edit', [
            'model' => $model,
		]);
	}

    public function actionLoadSlide(int $story_id, int $slide_id = -1)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($slide_id === -1) {
            $model = StorySlide::findFirstSlide($story_id);
        }
        else {
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
    ];

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

        $view = ($block->getType() === AbstractBlock::TYPE_VIDEO || $block->getType() === AbstractBlock::TYPE_VIDEOFILE) ? 'update_video' : 'update';
        return $this->renderAjax($view, [
            'model' => $form,
            'action' => ['editor/update-block/' . $block_type],
            'widgetStoryModel' => $widgetStoryModel,
        ]);
    }

    public function actionCreateBlock(int $slide_id, string $block_type)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $types = [
            AbstractBlock::TYPE_BUTTON => [
                'class' => ButtonBlock::class,
            ],
            AbstractBlock::TYPE_TRANSITION => [
                'class' => TransitionBlock::class,
            ],
            AbstractBlock::TYPE_TEXT => [
                'class' => TextBlock::class,
            ],
            AbstractBlock::TYPE_TEST => [
                'class' => TestBlock::class,
            ],
            AbstractBlock::TYPE_VIDEO => [
                'class' => VideoBlock::class,
            ],
            AbstractBlock::TYPE_IMAGE => [
                'class' => ImageBlock::class,
            ],
            AbstractBlock::TYPE_VIDEOFILE => [
                'class' => VideoFileBlock::class,
            ],
        ];
        if (!isset($types[$block_type])) {
            throw new DomainException($block_type . ' - Unknown block type');
        }

        $model = StorySlide::findSlide($slide_id);
        $slide = (new HtmlSlideReader($model->data))->load();
        $block = $slide->createBlock($types[$block_type]);
        $slide->addBlock($block);

        if ($block_type === AbstractBlock::TYPE_VIDEO) {
            Story::updateVideo($model->story_id, 1);
        }

        $html = (new HTMLWriter())->renderSlide($slide);
        $model->data = $html;
        $model->save(false, ['data']);

        return ['success' => true, 'block_id' => $block->getId()];
    }

    public function actionCreateSlideLink()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $form = new SlideLinkForm();
        $response = ['success' => false];
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $response['id'] = $this->editorService->createSlideLink($form->story_id, $form->link_slide_id, $form->slide_id);
                $response['success'] = true;
            }
            catch (Exception $ex) {
                $response['error'] = $ex->getMessage();
            }
        }
        return $response;
    }

    public function actionCreateSlideQuestion(int $story_id, int $question_id, int $current_slide_id = -1)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $slideID = $this->editorService->createSlideQuestion($story_id, $question_id, $current_slide_id);
        }
        catch (Exception $ex) {
            return ['success' => false, 'error' => $ex->getMessage()];
        }
        return ['success' => true, 'id' => $slideID];
    }

    public function actionNewCreateSlideQuestion(int $story_id, int $id, $question_params = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $test = StoryTest::findModel($id);
        $params = [
            'test-id' => $test->id,
        ];
        if ($question_params !== null) {
            $params['question_params'] = base64_encode($question_params);
        }
        try {
            $slideID = $this->editorService->newCreateSlideQuestion($story_id, $params);
            $this->storyLinksService->createTestLink($story_id, $test->id);
        }
        catch (Exception $ex) {
            return ['success' => false, 'error' => $ex->getMessage(), 'id' => $slideID];
        }
        return ['success' => true, 'id' => $slideID];
    }

    public function actionDeleteBlock(int $slide_id, string $block_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $this->editorService->deleteBlock($slide_id, $block_id);
        }
        catch (Exception $ex) {}
        return ['success' => true];
    }

    public function actionSlides(int $story_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = Story::findModel($story_id);
        return array_map(function(StorySlide $slide) {
            return [
                'id' => $slide->id,
                'slideNumber' => $slide->number,
                'isLink' => $slide->isLink(),
                'isQuestion' => $slide->isQuestion(),
                'linkSlideID' => $slide->link_slide_id,
                'isHidden' => $slide->isHidden(),
            ];
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
}


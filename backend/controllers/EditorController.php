<?php

namespace backend\controllers;

use backend\components\SlideModifier;
use backend\components\story\AbstractBlock;
use backend\components\story\ButtonBlock;
use backend\components\story\ImageBlock;
use backend\components\story\reader\HtmlSlideReader;
use backend\components\story\TestBlock;
use backend\components\story\TextBlock;
use backend\components\story\TransitionBlock;
use backend\components\story\VideoBlock;
use backend\components\story\writer\HTMLWriter;
use backend\models\editor\ButtonForm;
use backend\models\editor\ImageForm;
use backend\models\editor\ImageFromUrlForm;
use backend\models\editor\QuestionForm;
use backend\models\editor\SlideSourceForm;
use backend\models\editor\TestForm;
use backend\models\editor\TextForm;
use backend\models\editor\TransitionForm;
use backend\models\editor\VideoForm;
use common\models\StorySlide;
use common\models\StoryTest;
use DomainException;
use Yii;
use yii\filters\AccessControl;
use common\models\Story;
use common\services\StoryService;
use common\rbac\UserRoles;
use backend\services\StoryEditorService;
use yii\web\Controller;
use yii\web\Response;

class EditorController extends Controller
{

    protected $storyService;
    protected $editorService;

    public function __construct($id, $module, StoryService $storyService, StoryEditorService $editorService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->storyService = $storyService;
        $this->editorService = $editorService;
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
        $model = Story::findModel($id);
        $imageForm = new ImageForm();
        $imageForm->story_id = $model->id;
        $imageFromUrlForm = new ImageFromUrlForm();
        $imageFromUrlForm->story_id = $model->id;
        return $this->render('edit', [
            'model' => $model,
            'imageModel' => $imageForm,
            'imageFromUrlModel' => $imageFromUrlForm,
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
        $slideData = (new SlideModifier())->addImageParams($model->data);
        return [
            'id' => $model->id,
            'status' => $model->status,
            'data' => $slideData,
            'blockNumber' => count($model->storySlideBlocks),
            'number' => $model->number,
        ];
    }

    public function actionSlideBlocks(int $slide_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = StorySlide::findSlide($slide_id);
        $reader = new HtmlSlideReader($model->data);
        $slide = $reader->load();
        return $slide->getBlocksArray();
    }

    public function actionUpdateText()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $form = new TextForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $this->editorService->updateBlock($form);
            return ['success' => true];
        }
        return $form->getErrors();
    }

    public function actionUpdateImage()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $form = new ImageForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $this->editorService->updateBlock($form);
            return ['success' => true];
        }
        return $form->getErrors();
    }

    public function actionUpdateButton()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $form = new ButtonForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $this->editorService->updateBlock($form);
            return ['success' => true];
        }
        return $form->getErrors();
    }

    public function actionUpdateTransition()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $form = new TransitionForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $this->editorService->updateBlock($form);
            return ['success' => true];
        }
        return $form->getErrors();
    }

    public function actionUpdateTest()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $form = new TestForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $this->editorService->updateBlock($form);
            return ['success' => true];
        }
        return $form->getErrors();
    }

    public function actionUpdateVideo()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $form = new VideoForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $this->editorService->updateBlock($form);
            return ['success' => true];
        }
        return $form->getErrors();
    }

    public function actionUpdateHtml()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $form = new QuestionForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $this->editorService->updateBlock($form);
            return ['success' => true];
        }
        return $form->getErrors();
    }

    public function actionForm(int $slide_id, string $block_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = StorySlide::findSlide($slide_id);
        $slide = (new HtmlSlideReader($model->data))->load();
        $block = $slide->findBlockByID($block_id);

        $types = [
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
            ],
        ];
        $block_type = $block->getType();
        if (!isset($types[$block_type])) {
            throw new DomainException($block_type . ' - Unknown block type');
        }

        $form = Yii::createObject($types[$block_type]);
        $form->slide_id = $model->id;
        $form->block_id = $block_id;

        $values = $block->getValues();
        $form->load($values, '');

        if (($block->getType() === AbstractBlock::TYPE_TRANSITION) && $form->transition_story_id === null) {
            $form->transition_story_id = $model->story_id;
        }

        return $this->renderAjax('_form', [
            'model' => $form,
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

        return ['success' => true];
    }

    public function actionCreateSlide(int $story_id, int $current_slide_id = -1)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $slideID = $this->editorService->createSlide($story_id, $current_slide_id);
        return ['success' => true, 'id' => $slideID];
    }

    public function actionCreateSlideLink(int $story_id, int $link_slide_id, int $current_slide_id = -1)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $linkSlideID = $this->editorService->createSlideLink($story_id, $link_slide_id, $current_slide_id);
        }
        catch (\Exception $ex) {
            return ['success' => false, 'error' => $ex->getMessage()];
        }
        return ['success' => true, 'id' => $linkSlideID];
    }

    public function actionCreateSlideQuestion(int $story_id, int $question_id, int $current_slide_id = -1)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $slideID = $this->editorService->createSlideQuestion($story_id, $question_id, $current_slide_id);
        }
        catch (\Exception $ex) {
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
            $params['question_params'] = $question_params;
        }
        try {
            $slideID = $this->editorService->newCreateSlideQuestion($story_id, $params);
        }
        catch (\Exception $ex) {
            return ['success' => false, 'error' => $ex->getMessage()];
        }
        return ['success' => true, 'id' => $slideID];
    }

    public function actionCopySlide(int $slide_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $slideID = $this->editorService->copySlide($slide_id);
        }
        catch (\Exception $ex) {
            return ['success' => false, 'error' => $ex->getMessage()];
        }
        return ['success' => true, 'id' => $slideID];
    }

    public function actionDeleteBlock(int $slide_id, string $block_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $this->editorService->deleteBlock($slide_id, $block_id);
        return ['success' => true];
    }

    public function actionDeleteSlide(int $slide_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $this->editorService->deleteSlide($slide_id);
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

    public function actionSlideVisible(int $slide_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = StorySlide::findSlide($slide_id);
        if ($model->status === StorySlide::STATUS_VISIBLE) {
            $model->status = StorySlide::STATUS_HIDDEN;
        }
        else {
            $model->status = StorySlide::STATUS_VISIBLE;
        }
        $model->save(false, ['status']);
        return ['success' => true, 'status' => $model->status];
    }

    public function actionSlideSource(int $slide_id)
    {
        $model = new SlideSourceForm($slide_id);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->saveSlideSource();
        }
        else {
            $model->loadSlideSource();
        }
        return $this->renderAjax('_slide_source', ['model' => $model]);
    }

}

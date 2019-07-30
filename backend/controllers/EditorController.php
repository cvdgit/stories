<?php

namespace backend\controllers;

use backend\components\story\AbstractBlock;
use backend\components\story\ButtonBlock;
use backend\components\story\reader\HtmlSlideReader;
use backend\components\story\TextBlock;
use backend\components\story\TransitionBlock;
use backend\components\story\writer\HTMLWriter;
use backend\models\editor\ButtonForm;
use backend\models\editor\ImageForm;
use backend\models\editor\SlideSourceForm;
use backend\models\editor\TextForm;
use backend\models\editor\TransitionForm;
use common\models\StorySlide;
use DomainException;
use Yii;
use yii\filters\AccessControl;
use common\models\Story;
use common\services\StoryService;
use common\rbac\UserRoles;
use backend\services\StoryEditorService;
use backend\components\StoryEditor;
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
        return $this->render('edit', [
            'model' => $model,
		]);
	}

    public function actionGetSlideByIndex(int $story_id, int $slide_index = -1)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($slide_index === -1) {
            $model = StorySlide::findFirstSlide($story_id);
        }
        else {
            $model = StorySlide::findSlide($story_id, $slide_index);
        }
        return $model;
    }

    public function actionGetSlideBlocks(int $story_id, int $slide_index)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = StorySlide::findSlide($story_id, $slide_index);
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

    /**
     * @param int $story_id
     * @param int $slide_index
     * @param string $block_id
     * @return string
     * @throws yii\base\InvalidConfigException
     */
    public function actionForm(int $story_id, int $slide_index, string $block_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = StorySlide::findSlide($story_id, $slide_index);
        $reader = new HtmlSlideReader($model->data);
        $slide = $reader->load();
        $block = $slide->findBlockByID($block_id);
        $block_type = $block->getType();

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
        ];
        if (!isset($types[$block_type])) {
            throw new DomainException($block_type . ' - Unknown block type');
        }

        $form = Yii::createObject($types[$block_type]);
        $form->story_id = $model->story_id;
        $form->slide_index = $slide_index;
        $form->block_id = $block_id;
        $values = $block->getValues();
        $form->load($values, '');

        return $this->renderAjax($form->view, [
            'model' => $form,
        ]);
    }

    public function actionCreateBlock(int $story_id, int $slide_index, string $block_type)
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
        ];
        if (!isset($types[$block_type])) {
            throw new DomainException($block_type . ' - Unknown block type');
        }

        $model = StorySlide::findSlide($story_id, $slide_index);
        $reader = new HtmlSlideReader($model->data);
        $slide = $reader->load();

        $block = $slide->createBlock($types[$block_type]);
        $slide->addBlock($block);

        $writer = new HTMLWriter();
        $html = $writer->renderSlide($slide);

        $model->data = $html;
        $model->save(false, ['data']);

        return ['success' => true];
    }

    public function actionCreateSlide(int $story_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $slideNumber = $this->editorService->createSlide($story_id);
        return ['success' => true, 'slideNumber' => $slideNumber];
    }

    public function actionDeleteBlock(int $story_id, int $slide_index, string $block_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $this->editorService->deleteBlock($story_id, $slide_index, $block_id);
        return ['success' => true];
    }

    public function actionDeleteSlide(int $story_id, int $slide_index)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $this->editorService->deleteSlide($story_id, $slide_index);
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
            ];
        }, $model->storySlides);
    }

    public function actionSlideVisible(int $story_id, int $slide_index)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = StorySlide::findSlide($story_id, $slide_index);
        if ($model->status === StorySlide::STATUS_VISIBLE) {
            $model->status = StorySlide::STATUS_HIDDEN;
        }
        else {
            $model->status = StorySlide::STATUS_VISIBLE;
        }
        $model->save(false, ['status']);
        return ['success' => true, 'status' => $model->status];
    }

    public function actionSlideSource(int $story_id, int $slide_index)
    {
        $model = new SlideSourceForm($story_id, $slide_index);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->saveSlideSource();
        }
        else {
            $model->loadSlideSource();
        }
        return $this->renderAjax('_slide_source', ['model' => $model]);
    }

}

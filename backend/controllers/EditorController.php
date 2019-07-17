<?php

namespace backend\controllers;

use backend\components\story\AbstractBlock;
use backend\components\story\ButtonBlock;
use backend\components\story\reader\HTMLReader;
use backend\components\story\TransitionBlock;
use backend\models\editor\ButtonForm;
use backend\models\editor\ImageForm;
use backend\models\editor\SlidePropsForm;
use backend\models\editor\TextForm;
use backend\models\editor\TransitionForm;
use DomainException;
use Yii;
use yii\filters\AccessControl;
use common\models\Story;
use common\services\StoryService;
use common\rbac\UserRoles;
use backend\services\StoryEditorService;
use backend\components\StoryHtmlReader;
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

    public function actionGetSlideByIndex(int $story_id, int $slide_index)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = Story::findModel($story_id);
        $editor = new StoryEditor($model->body);
        $response['html'] = $editor->getSlideMarkup($slide_index);
        return $response;
    }

    public function actionGetSlideBlocks(int $story_id, int $slide_index)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = Story::findModel($story_id);
        $editor = new StoryEditor($model->body);
        return $editor->getSlideBlocksArray($slide_index);
    }

    public function actionUpdateText()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $form = new TextForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $this->editorService->updateSlideText($form);
            return ['success' => true];
        }
        return $form->getErrors();
    }

    public function actionUpdateImage()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $form = new ImageForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $this->editorService->updateSlideImage($form);
            return ['success' => true];
        }
        return $form->getErrors();
    }

    public function actionUpdateButton()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $form = new ButtonForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $this->editorService->updateSlideButton($form);
            return ['success' => true];
        }
        return $form->getErrors();
    }

    public function actionUpdateTransition()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $form = new TransitionForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $this->editorService->updateSlideTransition($form);
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

        $model = Story::findModel($story_id);
        $editor = new StoryEditor($model->body);
        $block = $editor->findBlockByID($slide_index, $block_id);
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
        $form->story_id = $model->id;
        $form->slide_index = $slide_index;
        $form->block_id = $block_id;

        $values = $editor->getBlockValues($block);

        $form->load($values, '');

        return $this->renderAjax($form->view, [
            'model' => $form,
        ]);
    }

    public function actionCreateBlock(int $story_id, int $slide_index, string $block_type)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $types = [
            AbstractBlock::TYPE_BUTTON,
            AbstractBlock::TYPE_TRANSITION,
        ];
        if (!in_array($block_type, $types, true)) {
            throw new DomainException('Unknown block type');
        }

        $model = Story::findModel($story_id);
        $editor = new StoryEditor($model->body);

        $editor->createBlock($slide_index, $block_type);

        $body = $editor->getStoryMarkup();
        $model->saveBody($body);

        return ['success' => true];
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
        $model = Story::findModel($story_id);
        $editor = new StoryEditor($model->body);
        $editor->deleteSlide($slide_index);
        $body = $editor->getStoryMarkup();
        $model->saveBody($body);
        return ['success' => true];
    }

    public function actionSlides(int $story_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = Story::findModel($story_id);
        $editor = new StoryEditor($model->body);
        return $editor->getSlides();
    }

}

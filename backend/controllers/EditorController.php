<?php

namespace backend\controllers;

use backend\components\story\AbstractBlock;
use backend\components\story\reader\HTMLReader;
use backend\models\editor\ImageForm;
use backend\models\editor\TextForm;
use DomainException;
use Yii;
use yii\filters\AccessControl;
use common\models\Story;
use common\services\StoryService;
use common\rbac\UserRoles;
use backend\services\StoryEditorService;
use backend\components\StoryHtmlReader;
use backend\components\StoryEditor;
use backend\models\SlideEditorForm;
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
        $reader = new StoryHtmlReader();
        $model = Story::findModel($id);
        $story = $reader->loadStoryFromHtml($model->body);
        return $this->render('edit', [
            'model' => $model,
            'story' => $story,
		]);
	}

    public function actionGetSlideByIndex(int $story_id, int $slide_index)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = Story::findModel($story_id);
        $reader = new HTMLReader($model->body);
        $story = $reader->load();

        $editor = new StoryEditor($story);
        $response['html'] = $editor->getSlideMarkup($slide_index);
        $response['story'] = $editor->getSlideValues($slide_index);
        return $response;
    }

    public function actionGetSlideBlocks(int $story_id, int $slide_index)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = Story::findModel($story_id);
        $reader = new HTMLReader($model->body);
        $story = $reader->load();

        $editor = new StoryEditor($story);
        return $editor->getSlideBlocksArray($slide_index);
    }

    public function actionUpdateText()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $editorModel = new SlideEditorForm();
        if ($editorModel->load(Yii::$app->request->post()) && $editorModel->validate()) {    
            $this->editorService->updateSlide($editorModel);
            return ['success' => true];
        }
        else {
            return $editorModel->getErrors();
        }
        return 'no';
    }

    public function actionUpdateImage()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $editorModel = new SlideEditorForm();
        if ($editorModel->load(Yii::$app->request->post()) && $editorModel->validate()) {
            $this->editorService->updateSlide($editorModel);
            return ['success' => true];
        }
        else {
            return $editorModel->getErrors();
        }
        return 'no';
    }

    public function actionLink()
    {
        return 'ok';
    }

    public function actionForm(int $story_id, int $slide_index, string $block_type)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $types = [
            AbstractBlock::TYPE_TEXT => TextForm::class,
            AbstractBlock::TYPE_IMAGE => ImageForm::class,
        ];

        if (!isset($types[$block_type])) {
            throw new DomainException('Unknown block type');
        }

        $model = Story::findModel($story_id);

        $form = new $types[$block_type];
        $form->story_id = $model->id;
        $form->slide_index = $slide_index;

        $view = '';
        if ($block_type === 'text') {
            $view = '_text_form';
        }
        else if ($block_type === 'image') {
            $view = '_image_form';
        }
        if ($view === '') {
            throw new DomainException('View not found');
        }

        $reader = new HTMLReader($model->body);
        $story = $reader->load();

        $editor = new StoryEditor($story);
        $response['story'] = $editor->getSlideValues($slide_index);


        $form->text = $response['story']['text'];
        $form->text_size = $response['story']['text_size'];
        $form->image = $response['story']['image'];

        return $this->renderAjax($view, [
            'model' => $form,
        ]);
    }

}

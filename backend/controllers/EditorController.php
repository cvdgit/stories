<?php

namespace backend\controllers;

use backend\components\story\reader\HTMLReader;
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

        $editorModel = new SlideEditorForm();
        $editorModel->story_id = $model->id;

        return $this->render('edit', [
            'model' => $model,
            'story' => $story,
            'editorModel' => $editorModel
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

    public function actionUpdateSlide()
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

}

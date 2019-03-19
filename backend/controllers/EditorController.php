<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\base\Model;
use yii\helpers\Html;
use yii\web\UploadedFile;

use common\models\Story;
use common\services\StoryService;
use common\rbac\UserRoles;
use backend\services\StoryEditorService;
use backend\components\StoryHtmlReader;
use backend\components\StoryEditor;
use backend\models\SlideEditorForm;

class EditorController extends \yii\web\Controller
{

    protected $storyService;
    protected $editorService;

    public function __construct($id, $module, StoryService $storyService, StoryEditorService $editorService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->storyService = $storyService;
        $this->editorService = $editorService;
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
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

        $model = Story::findOne($id);
        $story = $reader->loadStoryFromHtml($model->body);

        $editorModel = new SlideEditorForm();
        $editorModel->story_id = $model->id;

		return $this->render('edit', [
            'model' => $model,
            'story' => $story,
            'editorModel' => $editorModel,
		]);
	}

    public function actionGetSlideByIndex($story_id, $slide_index)
    {

        $reader = new StoryHtmlReader();

        $model = Story::findOne($story_id);
        $story = $reader->loadStoryFromHtml($model->body);

        $editor = new StoryEditor($story);
        $response['html'] = $editor->getSlideMarkup($slide_index);
        $response['story'] = $editor->getSlideValues($slide_index);

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $response;
    }

    public function actionUpdateSlide()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
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

}

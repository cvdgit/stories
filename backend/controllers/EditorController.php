<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\base\Model;
use yii\helpers\Html;

use common\models\Story;
use backend\components\StoryHtmlReader;
use backend\components\StoryEditor;
use backend\models\SlideEditorForm;

class EditorController extends \yii\web\Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'],
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

    public function actionSetSlideText()
    {

        $editorModel = new SlideEditorForm();
        $success = false;

        if ($editorModel->load(Yii::$app->request->post()) && $editorModel->validate()) {

            $reader = new StoryHtmlReader();

            $model = Story::findOne($editorModel->story_id);
            $story = $reader->loadStoryFromHtml($model->body);

            $editor = new StoryEditor($story);
            $editor->setSlideText($editorModel->slide_index, $editorModel->text);

            $body = '<div class="slides">' . $editor->getStoryMarkup() . '</div>';
            $model->saveBody($body);
            $success = true;
        }

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return ['success' => $success];
    }

}

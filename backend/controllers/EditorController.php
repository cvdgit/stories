<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\base\Model;
use common\models\Story;
use common\services\RevealService;
use backend\models\SlideEditorForm;

class EditorController extends \yii\web\Controller
{

    public $service;

    public function __construct($id, $module, RevealService $service, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
    }

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

        $story = Story::findOne($id);

        $model = new SlideEditorForm();
        $model->loadSlidesFromBody($story->body);

        if ($model->load(Yii::$app->request->post())) {
            $body = $this->service->wrapSlides(implode('', $model->slides));
            $story->saveBody($body);
            Yii::$app->session->setFlash('success', 'Изменения успешно сохранены');
        }

		return $this->render('edit', [
            'model' => $model,
		]);
	}

}

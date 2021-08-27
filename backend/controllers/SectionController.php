<?php

namespace backend\controllers;

use backend\components\BaseController;
use backend\models\section\CreateSectionForm;
use backend\models\section\UpdateSectionForm;
use common\models\SiteSection;
use common\rbac\UserRoles;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class SectionController extends BaseController
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::PERMISSION_MANAGE_SECTIONS],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $query = SiteSection::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new CreateSectionForm();
        if ($model->load(Yii::$app->request->post())) {
            $model->createSection();
            Yii::$app->session->setFlash('success', 'Раздел успешно изменен');
            return $this->redirect(['update', 'id' => $model->id]);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate(int $id)
    {
        /** @var UpdateSectionForm $model */
        $model = $this->findModel(UpdateSectionForm::class, $id);
        if ($model->load(Yii::$app->request->post())) {
            $model->updateSection();
            Yii::$app->session->setFlash('success', 'Раздел успешно изменен');
            return $this->refresh();
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete(int $id)
    {
        /** @var SiteSection $model */
        $model = $this->findModel(SiteSection::class, $id);
        $model->delete();
    }
}

<?php

namespace backend\controllers;

use backend\models\test\UpdateForm;
use common\models\StoryTest;
use common\rbac\UserRoles;
use backend\models\test\CreateForm;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class TestVariantController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::PERMISSION_MANAGE_TEST],
                    ],
                ],
            ],
        ];
    }

    public function actionCreate(int $parent_id)
    {
        $testModel = $this->findModel($parent_id);
        $model = new CreateForm($parent_id);
        $model->neo_question_id = $testModel->question_list_id;
        if ($model->load(Yii::$app->request->post())) {
            try {
                $model->createTestVariant();
                return Json::encode(['success' => true, 'params' => $model->getChildrenTestsAsArray()]);
            }
            catch (\Exception $ex) {
                return Json::encode(['success' => false, 'errors' => [$ex->getMessage()]]);
            }
        }
        return $this->renderAjax('create', ['model' => $model]);
    }

    public function actionUpdate(int $id)
    {
        $model = $this->findModel($id);
        $updateForm = new UpdateForm($model);
        $updateForm->neo_question_id = $model->question_list_id;
        if ($updateForm->load(Yii::$app->request->post())) {
            try {
                $updateForm->updateTestVariant();
                return Json::encode(['success' => true, 'params' => $model->getParent()->getChildrenTestsAsArray()]);
            }
            catch (\Exception $ex) {
                return Json::encode(['success' => false, 'errors' => [$ex->getMessage()]]);
            }
        }
        return $this->renderAjax('update', ['model' => $updateForm]);
    }

    protected function findModel($id)
    {
        if (($model = StoryTest::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionDelete(int $id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $this->findModel($id)->delete();
        return ['success' => true];
    }

}
<?php

namespace backend\controllers;

use backend\models\CreateWordForm;
use backend\models\UpdateWordForm;
use common\models\TestWord;
use common\models\TestWordList;
use common\rbac\UserRoles;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class WordController extends Controller
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

    /**
     * @param int $list_id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionCreate(int $list_id)
    {
        $listModel = $this->findListModel($list_id);
        $model = new CreateWordForm($listModel);
        if ($model->load(Yii::$app->request->post())) {
            try {
                $model->createWord();
                return Json::encode(['success' => true, 'params' => $model->getTestWordsAsArray()]);
            }
            catch (\Exception $ex) {
                return Json::encode(['success' => false, 'errors' => [$ex->getMessage()]]);
            }
        }
        return $this->renderAjax('create', ['model' => $model]);
    }

    /**
     * @param $id
     * @return TestWord|null
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = TestWord::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @param $id
     * @return TestWordList|null
     * @throws NotFoundHttpException
     */
    protected function findListModel($id)
    {
        if (($model = TestWordList::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id)
    {
        $model = $this->findModel($id);
        $updateForm = new UpdateWordForm($model);
        if ($updateForm->load(Yii::$app->request->post())) {
            try {
                $updateForm->updateWord();
                return Json::encode(['success' => true, 'params' => $model->wordList->getTestWordsAsArray()]);
            }
            catch (\Exception $ex) {
                return Json::encode(['success' => false, 'errors' => [$ex->getMessage()]]);
            }
        }
        return $this->renderAjax('update', ['model' => $updateForm]);
    }

    /**
     * @param int $id
     * @return bool[]
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete(int $id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $this->findModel($id)->delete();
        return ['success' => true];
    }

}
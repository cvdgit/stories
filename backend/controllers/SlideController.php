<?php


namespace backend\controllers;


use backend\models\SlidesOrder;
use common\models\StorySlide;
use common\rbac\UserRoles;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

class SlideController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::PERMISSION_MANAGE_STORIES],
                    ],
                ],
            ],
        ];
    }

    public function actionSlideRelations(int $slide_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $slide = StorySlide::findSlide($slide_id);
        return $slide->neoSlideRelations;
    }

    public function actionSaveOrder()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $form = new SlidesOrder();
        $result = ['success' => false, 'errors' => ''];
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $form->saveSlidesOrder();
                $result['success'] = true;
            }
            catch (\Exception $ex) {
                $result['errors'] = $ex->getMessage();
            }
        }
        else {
            $result['errors'] = $form->errors;
        }
        return $result;
    }

}
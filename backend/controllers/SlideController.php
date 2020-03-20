<?php


namespace backend\controllers;


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
        $relation = null;
        if (count($slide->neoSlideRelations) > 0) {
            $relation = $slide->neoSlideRelations[0];
        }
        return $relation;
    }

}
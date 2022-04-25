<?php

namespace backend\controllers;

use backend\components\editor\SlideListResponse;
use common\models\Lesson;
use common\models\StorySlide;
use common\rbac\UserRoles;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\web\Controller;

class LessonController extends Controller
{

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

    /**
     * @throws NotFoundHttpException
     */
    public function actionSlides(int $id): array
    {
        $this->response->format = Response::FORMAT_JSON;

        if (($lessonModel = Lesson::findOne($id)) === null) {
            throw new NotFoundHttpException('Lesson not found');
        }

        $slides = array_map(static function(StorySlide $slide) {
            return (new SlideListResponse($slide))->asArray();
        }, $lessonModel->slides);

        $blocks = $lessonModel->getLessonBlocks()
            ->indexBy('slide_id')
            ->all();

        return array_map(static function($slide) use ($blocks) {
            $id = $slide['id'];
            $slide['slideNumber'] = $blocks[$id]->order;
            $slide['number'] = $blocks[$id]->order;
            return $slide;
        }, $slides);
    }
}

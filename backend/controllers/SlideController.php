<?php

declare(strict_types=1);

namespace backend\controllers;

use backend\components\SlideModifier;
use backend\models\editor\SlideSourceForm;
use common\models\Story;
use common\models\StorySlide;
use common\rbac\UserRoles;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;

class SlideController extends Controller
{
    public function behaviors(): array
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

    /**
     * @throws NotFoundHttpException
     */
    public function actionSlideRelations(int $slide_id): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $slide = StorySlide::findSlide($slide_id);
        if ($slide === null) {
            throw new NotFoundHttpException('История не найдена');
        }
        return $slide->neoSlideRelations;
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionSlides(int $story_id, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $story = Story::findOne($story_id);
        if ($story === null) {
            throw new NotFoundHttpException('История не найдена');
        }
        return array_map(static function(StorySlide $slide) use ($story) {
            $data = (new SlideModifier($slide->id, $slide->data))
                ->addDescription()
                ->render();
            return [
                'id' => $slide->id,
                'slideNumber' => $slide->number,
                'data' => $data,
                'story' => $story->title,
            ];
        }, $story->storySlides);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionSource(int $id, Request $request, Response $response)
    {
        $slide = StorySlide::findOne($id);
        if ($slide === null) {
            throw new NotFoundHttpException('Слайд не найден');
        }

        $sourceForm = new SlideSourceForm();
        $sourceForm->source = $slide->data;
        $sourceForm->slide_id = $slide->id;

        if ($sourceForm->load($request->post())) {
            $response->format = Response::FORMAT_JSON;

            if (!$sourceForm->validate()) {
                return ['success' => false];
            }

            try {
                $slide->data = $sourceForm->source;
                if (!$slide->save()) {
                    throw new \DomainException('Ошибка при сохранении');
                }
                return ['success' => true];
            } catch (\Exception $exception) {
                \Yii::$app->errorHandler->logException($exception);
                return ['success' => false, 'message' => $exception->getMessage()];
            }
        }

        return $this->renderAjax('source', [
            'formModel' => $sourceForm,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace frontend\controllers;

use common\models\Story;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class EduStoryController extends Controller
{

    /**
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): string
    {
        if (($story = Story::findOne($id)) === null) {
            throw new NotFoundHttpException('История не найдена');
        }
        return $this->renderAjax('view', [
            'story' => $story,
        ]);
    }
}

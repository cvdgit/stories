<?php

declare(strict_types=1);

namespace frontend\modules\LearningPath\controllers;

use frontend\modules\LearningPath\models\LearningPath;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class DefaultController extends Controller
{
    /**
     * @throws NotFoundHttpException
     */
    public function actionIndex(string $id): string
    {
        $learningPath = LearningPath::findOne($id);
        if ($learningPath === null) {
            throw new NotFoundHttpException('Карта знаний не найдена');
        }
        return $this->render('index', [
            'learningPathId' => $learningPath->uuid,
        ]);
    }

    public function actionInit(string $id, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $learningPath = LearningPath::findOne($id);
        if ($learningPath === null) {
            return ['success' => false, 'Карта знаний не найдена'];
        }
        return ['success' => true, 'data' => $learningPath->payload];
    }
}

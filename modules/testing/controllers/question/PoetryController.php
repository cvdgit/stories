<?php

declare(strict_types=1);

namespace modules\testing\controllers\question;

use modules\testing\forms\PoetryForm;
use modules\testing\models\Testing;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class PoetryController extends Controller
{
    /**
     * @throws NotFoundHttpException
     */
    public function actionCreate(int $test_id)
    {
        $testing = Testing::findOne($test_id);
        if ($testing === null) {
            throw new NotFoundHttpException('Тестирование не найдено');
        }
        $poetryForm = new PoetryForm();
        return $this->render('create', [
            'testing' => $testing,
            'formModel' => $poetryForm,
        ]);
    }
}

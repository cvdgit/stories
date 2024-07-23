<?php

declare(strict_types=1);

namespace frontend\controllers;

use frontend\models\ContactRequestForm;
use Yii;
use yii\captcha\CaptchaAction;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\ErrorAction;

class NewSchoolController extends Controller
{
    public $layout = 'new-school';

    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
            'captcha' => [
                'class' => CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        if (Yii::$app->request->pathInfo !== '') {
            return $this->redirect(['/']);
        }
        return $this->render('index', [
            'contactRequestModel' => new ContactRequestForm(),
        ]);
    }
}

<?php

namespace frontend\controllers;

use frontend\models\ContactRequestForm;
use Yii;
use yii\web\Controller;

class SchoolController extends Controller
{

    public $layout = 'school';

    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
            'captcha' => [
                'class' => \yii\captcha\CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index', [
            'contactRequestModel' => new ContactRequestForm(),
        ]);
    }
}
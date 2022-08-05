<?php

namespace frontend\controllers;

use frontend\models\ContactRequestForm;
use Yii;
use yii\captcha\CaptchaAction;
use yii\web\Controller;
use yii\web\ErrorAction;

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
        return $this->render('index', [
            'contactRequestModel' => new ContactRequestForm(),
        ]);
    }
}

<?php

namespace frontend\components;

use yii\filters\AccessControl;
use yii\web\Controller;

class UserController extends Controller
{

    public $layout = 'profile';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['user'],
                    ],
                ],
            ],
        ];
    }

}
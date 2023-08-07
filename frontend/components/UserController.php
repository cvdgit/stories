<?php

declare(strict_types=1);

namespace frontend\components;

use yii\filters\AccessControl;
use yii\web\Controller;

class UserController extends Controller
{
    public $layout = 'profile';

    public function behaviors(): array
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

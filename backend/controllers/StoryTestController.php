<?php

declare(strict_types=1);

namespace backend\controllers;

use common\rbac\UserRoles;
use yii\filters\AccessControl;
use yii\web\Controller;

class StoryTestController extends Controller
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

    public function actionIndex(int $id)
    {
        return $this->render('index');
    }
}

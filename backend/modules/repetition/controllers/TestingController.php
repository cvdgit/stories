<?php

declare(strict_types=1);

namespace backend\modules\repetition\controllers;

use backend\modules\repetition\Repetition\TestingCreate\CreateAction;
use backend\modules\repetition\Repetition\TestingDelete\DeleteAction;
use backend\modules\repetition\Repetition\TestingList\ListAction;
use common\rbac\UserRoles;
use yii\filters\AccessControl;
use yii\web\Controller;

class TestingController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::ROLE_ADMIN],
                    ],
                ],
            ],
        ];
    }

    public function actions(): array
    {
        return [
            'create' => CreateAction::class,
            'list' => ListAction::class,
            'delete' => DeleteAction::class,
        ];
    }
}

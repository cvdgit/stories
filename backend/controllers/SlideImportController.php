<?php

declare(strict_types=1);

namespace backend\controllers;

use backend\actions\SlideImport\ImportAction;
use common\rbac\UserRoles;
use yii\filters\AccessControl;
use yii\web\Controller;

class SlideImportController extends Controller
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

    public function actions(): array
    {
        return [
            'import' => ImportAction::class,
        ];
    }
}

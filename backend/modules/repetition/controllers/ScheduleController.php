<?php

declare(strict_types=1);

namespace backend\modules\repetition\controllers;

use backend\modules\repetition\Schedule\CreateSchedule\CreateAction;
use backend\modules\repetition\Schedule\IndexAction;
use backend\modules\repetition\Schedule\Update\UpdateAction;
use common\rbac\UserRoles;
use yii\filters\AccessControl;
use yii\web\Controller;

class ScheduleController extends Controller
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
            'index' => IndexAction::class,
            'create' => CreateAction::class,
            'update' => UpdateAction::class,
        ];
    }
}

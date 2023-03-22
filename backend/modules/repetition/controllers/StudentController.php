<?php

declare(strict_types=1);

namespace backend\modules\repetition\controllers;

use backend\modules\repetition\Students\CreateRepetition\CreateRepetitionAction;
use backend\modules\repetition\Students\ListAction;
use backend\modules\repetition\Students\NextRepetition\NextRepetitionAction;
use backend\modules\repetition\Students\View\StudentViewAction;
use common\rbac\UserRoles;
use yii\filters\AccessControl;
use yii\web\Controller;

class StudentController extends Controller
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
            'list' => ListAction::class,
            'view' => StudentViewAction::class,
            'create-repetition' => CreateRepetitionAction::class,
            'next-repetition' => NextRepetitionAction::class,
        ];
    }
}

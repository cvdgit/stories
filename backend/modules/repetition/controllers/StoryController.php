<?php

declare(strict_types=1);

namespace backend\modules\repetition\controllers;

use backend\modules\repetition\Repetition\StoryCreate\CreateRepetitionAction;
use backend\modules\repetition\Repetition\StoryStart\StartRepetitionAction;
use common\rbac\UserRoles;
use yii\filters\AccessControl;
use yii\web\Controller;

class StoryController extends Controller
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
            'create' => CreateRepetitionAction::class,
            'start' => StartRepetitionAction::class,
        ];
    }
}

<?php

declare(strict_types=1);

namespace backend\controllers\test;

use backend\Testing\Questions\Grouping\Create\CreateGroupingAction;
use backend\Testing\Questions\Grouping\Update\UpdateGroupingAction;
use common\rbac\UserRoles;
use yii\filters\AccessControl;
use yii\web\Controller;

class GroupingController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::PERMISSION_MANAGE_TEST],
                    ],
                ],
            ],
        ];
    }

    public function actions(): array
    {
        return [
            "create" => CreateGroupingAction::class,
            "update" => UpdateGroupingAction::class,
        ];
    }
}

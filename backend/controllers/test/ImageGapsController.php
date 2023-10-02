<?php

declare(strict_types=1);

namespace backend\controllers\test;

use backend\Testing\Questions\ImageGaps\Create\CreateAction;
use backend\Testing\Questions\ImageGaps\Fragment\FragmentAction;
use backend\Testing\Questions\ImageGaps\Update\UpdateAction;
use common\rbac\UserRoles;
use yii\filters\AccessControl;
use yii\web\Controller;

class ImageGapsController extends Controller
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
            'create' => CreateAction::class,
            'update' => UpdateAction::class,
            'fragment' => FragmentAction::class,
        ];
    }
}

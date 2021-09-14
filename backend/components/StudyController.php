<?php

namespace backend\components;

use common\rbac\UserRoles;
use yii\filters\AccessControl;

class StudyController extends BaseController
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::PERMISSION_MANAGE_STUDY],
                    ],
                ],
            ],
        ];
    }
}

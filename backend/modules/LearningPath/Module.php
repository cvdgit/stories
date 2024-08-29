<?php

namespace backend\modules\LearningPath;

use common\rbac\UserRoles;
use Yii;
use yii\filters\AccessControl;

/**
 * edu module definition class
 */
class Module extends \yii\base\Module
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
            ]
        ];
    }
}

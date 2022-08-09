<?php

declare(strict_types=1);

namespace modules\edu\components;

use common\rbac\UserRoles;
use modules\edu\models\EduUserAccess;
use Yii;

class EduAccessChecker
{

    public function canUserAccess(int $userId = null): bool
    {
        if ($userId === null) {
            return false;
        }
        $webUser = Yii::$app->user;
        return $webUser->can(UserRoles::PERMISSION_EDU_ACCESS, ['access' => EduUserAccess::findUserAccess($userId)])
            || $webUser->can(UserRoles::ROLE_TEACHER);
    }
}

<?php


namespace common\rbac;


use common\models\Story;
use common\models\User;
use Yii;

class UserPermissions
{

    public static function canViewStory(Story $story): bool
    {
        if (!$story->bySubscription()) {
            return true;
        }

        $webUser = Yii::$app->user;
        if ($webUser->isGuest) {
            return false;
        }

        if ($webUser->can(UserRoles::PERMISSION_MANAGE_STORIES)) {
            return true;
        }

        /** @var $user User */
        $user = $webUser->identity;
        if ($user->hasSubscription()) {
            return true;
        }

        return false;
    }

}
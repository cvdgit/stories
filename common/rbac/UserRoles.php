<?php

namespace common\rbac;

use Yii;

class UserRoles
{
    const ROLE_USER = 'user';
    const ROLE_MODERATOR = 'moderator';
    const ROLE_ADMIN = 'admin';

	const PERMISSION_ADMIN_PANEL = 'adminPanel';
	const PERMISSION_MANAGE_CATEGORIES = 'manageCategories';
	const PERMISSION_EDITOR_ACCESS = 'accessEditor';
	const PERMISSION_FEEDBACK_ACCESS = 'accessFeedback';
	const PERMISSION_STATISTICS_ACCESS = 'accessStatistics';
    const PERMISSION_TAGS_ACCESS = 'accessTags';
	const PERMISSION_MANAGE_STORIES = 'manageStories';
	const PERMISSION_MANAGE_USERS = 'manageUsers';
	const PERMISSION_MANAGE_RATES = 'manageRates';
    const PERMISSION_MANAGE_COMMENTS = 'manageComments';
    const PERMISSION_MANAGE_NEWS = 'manageNews';
    const PERMISSION_MANAGE_TEST = 'manageTest';

    public static function isModerator($userID)
    {
        if ($userID === null) {
            return false;
        }
        $role = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
        $role = array_shift($role);
        return $role->name === self::ROLE_MODERATOR;
    }

}

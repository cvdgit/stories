<?php
namespace common\rbac;

use Yii;
use yii\rbac\Rule;
use common\models\User;

/**
 * Checks if user group matches
 */
class UserGroupRule extends Rule
{
    public $name = 'userGroup';

    public function execute($user, $item, $params)
    {
        if (!Yii::$app->user->isGuest) {
            $group = Yii::$app->user->identity->group;
            if ($item->name === 'admin') {
                return $group == User::GROUP_ADMIN;
            } elseif ($item->name === 'author') {
                return $group == User::GROUP_ADMIN || $group == User::GROUP_AUTHOR;
            }
        }
        return false;
    }
}
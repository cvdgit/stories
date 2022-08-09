<?php

declare(strict_types=1);

namespace modules\edu\rbac;

use yii\rbac\Item;
use yii\rbac\Rule;

class EduAccessRule extends Rule
{

    public $name = 'eduAccessRule';

    /**
     * @param string|int $user the user ID.
     * @param Item $item the role or permission that this rule is associated width.
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return bool a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params): bool
    {
        return isset($params['access']) && (int)$params['access']->user_id === (int)$user;
    }
}

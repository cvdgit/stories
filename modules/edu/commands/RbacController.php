<?php

declare(strict_types=1);

namespace modules\edu\commands;

use common\rbac\UserRoles;
use modules\edu\rbac\EduAccessRule;
use Yii;
use yii\console\Controller;

class RbacController extends Controller
{

    public function actionInit(): void
    {
        $auth = Yii::$app->authManager;

        //if (($rule = $auth->getRule('eduAccess')) === null) {
            //die('ok');
        //    $auth->remove($rule);
        //}

        $rule = new EduAccessRule();
        $auth->add($rule);

        //if (($permission = $auth->getPermission(UserRoles::PERMISSION_EDU_ACCESS)) !== null) {
        //    $auth->remove($permission);
        //}

        $permission = $auth->createPermission(UserRoles::PERMISSION_EDU_ACCESS);
        $permission->description = 'Доступ к разделу Обучение';
        $permission->ruleName = $rule->name;
        $auth->add($permission);

        $userRole = $auth->getRole(UserRoles::ROLE_USER);
        $auth->addChild($userRole, $permission);

        $this->stdout('Done!' . PHP_EOL);
    }
}

<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll();

        $user = $auth->createRole('user');
        $moderator = $auth->createRole('moderator');
        $admin = $auth->createRole('admin');

        // $createStory = $auth->createPermission('createStory');
        // $updateStory = $auth->createPermission('updateStory');
        // $deleteStory = $auth->createPermission('deleteStory');
        //$manageStories = $auth->createPermission('manageStories');
        //$auth->add($manageStories);

        //$userGroupRule = new \common\rbac\UserGroupRule;
        //$auth->add($userGroupRule);
        //$author->ruleName = $userGroupRule->name;
        //$admin->ruleName  = $userGroupRule->name;

        $auth->add($user);
        $auth->add($moderator);
        $auth->add($admin);

        $auth->addChild($moderator, $user);
        $auth->addChild($admin, $moderator);

        $auth->assign($moderator, 4);
        $auth->assign($admin, 1);


/*
        // добавляем разрешение "createStory"
        $createStory = $auth->createPermission('createStory');
        $createStory->description = 'Создать историю';
        $auth->add($createStory);

        // добавляем разрешение "updateStory"
        $updateStory = $auth->createPermission('updateStory');
        $updateStory->description = 'Изменить историю';
        $auth->add($updateStory);

        // добавляем разрешение "deleteStory"
        $deleteStory = $auth->createPermission('deleteStory');
        $deleteStory->description = 'Удалить историю';
        $auth->add($deleteStory);

        // добавляем роль "author" и даём роли разрешение "createStory"
        $author = $auth->createRole('author');
        $auth->add($author);
        $auth->addChild($author, $createStory);

        // добавляем роль "author" и даём роли разрешение "updateStory"
        $author = $auth->createRole('author');
        $auth->add($author);
        $auth->addChild($author, $updateStory);

        // добавляем роль "author" и даём роли разрешение "deleteStory"
        $author = $auth->createRole('author');
        $auth->add($author);
        $auth->addChild($author, $deleteStory);

        $userGroupRule = new \common\rbac\UserGroupRule;
        $auth->add($userGroupRule);

        $author = $auth->createRole('author');
        $author->ruleName = $userGroupRule->name;
        $auth->add($author);


        // добавляем роль "admin" и даём роли разрешение "updatePost"
        // а также все разрешения роли "author"
        $admin = $auth->createRole('admin');
        $admin->ruleName = $userGroupRule->name;
        $auth->add($admin);
        $auth->addChild($admin, $author);


        // add the rule
        $rule = new \common\rbac\AuthorRule;
        $auth->add($rule);

        // добавляем разрешение "updateOwnStory" и привязываем к нему правило.
        $updateOwnStory = $auth->createPermission('updateOwnStory');
        $updateOwnStory->description = 'Редактировать свою историю';
        $updateOwnStory->ruleName = $rule->name;
        $auth->add($updateOwnStory);

        // "updateOwnStory" будет использоваться из "updateStory"
        $auth->addChild($updateOwnStory, $updateStory);

        // разрешаем "автору" обновлять его посты
        $auth->addChild($author, $updateOwnStory);


        // Назначение ролей пользователям. 1 и 2 это IDs возвращаемые IdentityInterface::getId()
        // обычно реализуемый в модели User.
        $auth->assign($author, 1);
        $auth->assign($admin, 2);
        */
    }
}

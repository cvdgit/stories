<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\rbac\UserRoles;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;

        $moderatorRole = $auth->getRole('moderator');

        $manageTagsPermission = $auth->createPermission(UserRoles::PERMISSION_TAGS_ACCESS);
        $manageTagsPermission->description = 'Управление тэгами';
        $auth->add($manageTagsPermission);

        $auth->addChild($moderatorRole, $manageTagsPermission);

        $adminRole = $auth->getRole('admin');

        $manageRolesPermission = $auth->createPermission(UserRoles::PERMISSION_MANAGE_RATES);
        $manageRolesPermission->description = 'Управление подписками';
        $auth->add($manageRolesPermission);

        $auth->addChild($adminRole, $manageRolesPermission);

        $manageCommentsPermission = $auth->createPermission(UserRoles::PERMISSION_MANAGE_COMMENTS);
        $manageCommentsPermission->description = 'Управление комментариями';
        $auth->add($manageCommentsPermission);

        $auth->addChild($adminRole, $manageCommentsPermission);

        /*
        $auth->removeAll();

        $userRole = $auth->createRole('user');
        $userRole->description = 'Пользователь';
        $moderatorRole = $auth->createRole('moderator');
        $moderatorRole->description = 'Управление историями';
        $adminRole = $auth->createRole('admin');
        $adminRole->description = 'Администратор сайта';


        // $createStory = $auth->createPermission('createStory');
        // $updateStory = $auth->createPermission('updateStory');
        // $deleteStory = $auth->createPermission('deleteStory');
        
        $adminPanelPermission = $auth->createPermission(UserRoles::PERMISSION_ADMIN_PANEL);
        $adminPanelPermission->description = 'Доступ к панели администрирования';
        $auth->add($adminPanelPermission);

        $manageStoriesPermission = $auth->createPermission(UserRoles::PERMISSION_MANAGE_STORIES);
        $manageStoriesPermission->description = 'Управление историями';
        $auth->add($manageStoriesPermission);

        $manageCategoriesPermission = $auth->createPermission(UserRoles::PERMISSION_MANAGE_CATEGORIES);
        $manageCategoriesPermission->description = 'Управление категориями';
        $auth->add($manageCategoriesPermission);

        $accessEditorPermission = $auth->createPermission(UserRoles::PERMISSION_EDITOR_ACCESS);
        $accessEditorPermission->description = 'Доступ к редактору историй';
        $auth->add($accessEditorPermission);

        $accessFeedbackPermission = $auth->createPermission(UserRoles::PERMISSION_FEEDBACK_ACCESS);
        $accessFeedbackPermission->description = 'Доступ к опечаткам';
        $auth->add($accessFeedbackPermission);

        $accessStatisticsPermission = $auth->createPermission(UserRoles::PERMISSION_STATISTICS_ACCESS);
        $accessStatisticsPermission->description = 'Доступ к статистике';
        $auth->add($accessStatisticsPermission);

        $manageUsersPermission = $auth->createPermission(UserRoles::PERMISSION_MANAGE_USERS);
        $manageUsersPermission->description = 'Управление пользователями';
        $auth->add($manageUsersPermission);

        //$userGroupRule = new \common\rbac\UserGroupRule;
        //$auth->add($userGroupRule);
        //$author->ruleName = $userGroupRule->name;
        //$admin->ruleName  = $userGroupRule->name;

        $auth->add($userRole);
        $auth->add($moderatorRole);
        $auth->add($adminRole);

        $auth->addChild($moderatorRole, $adminPanelPermission);
        $auth->addChild($moderatorRole, $manageStoriesPermission);
        $auth->addChild($moderatorRole, $accessEditorPermission);
        $auth->addChild($moderatorRole, $accessFeedbackPermission);
        $auth->addChild($moderatorRole, $accessStatisticsPermission);

        $auth->addChild($adminRole, $manageCategoriesPermission);
        $auth->addChild($adminRole, $manageUsersPermission);

        $auth->addChild($moderatorRole, $userRole);
        $auth->addChild($adminRole, $moderatorRole);

        $auth->assign($moderatorRole, 4);
        $auth->assign($adminRole, 1);
        */



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

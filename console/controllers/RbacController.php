<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\rbac\UserRoles;
use yii\rbac\ManagerInterface;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;

        $auth->removeAll();

        $userRole = $auth->createRole('user');
        $userRole->description = 'Пользователь';
        $moderatorRole = $auth->createRole('moderator');
        $moderatorRole->description = 'Управление историями';
        $adminRole = $auth->createRole('admin');
        $adminRole->description = 'Администратор сайта';

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

        $manageNewsPermission = $auth->createPermission(UserRoles::PERMISSION_MANAGE_NEWS);
        $manageNewsPermission->description = 'Управление новостями';
        $auth->add($manageNewsPermission);

        $manageTestsPermission = $auth->createPermission(UserRoles::PERMISSION_MANAGE_TEST);
        $manageTestsPermission->description = 'Управление тестами';
        $auth->add($manageTestsPermission);

        $auth->add($userRole);
        $auth->add($moderatorRole);
        $auth->add($adminRole);

        $auth->addChild($moderatorRole, $adminPanelPermission);
        $auth->addChild($moderatorRole, $manageStoriesPermission);
        $auth->addChild($moderatorRole, $accessEditorPermission);
        $auth->addChild($moderatorRole, $accessFeedbackPermission);
        $auth->addChild($moderatorRole, $accessStatisticsPermission);
        $auth->addChild($moderatorRole, $manageTestsPermission);

        $auth->addChild($adminRole, $manageCategoriesPermission);
        $auth->addChild($adminRole, $manageUsersPermission);
        $auth->addChild($adminRole, $manageNewsPermission);

        $auth->addChild($moderatorRole, $userRole);
        $auth->addChild($adminRole, $moderatorRole);

        //$moderatorRole = $auth->getRole('moderator');

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

        //$auth->assign($moderatorRole, 4);
        $auth->assign($adminRole, 1);

        $this->actionInitSection();
        $this->actionCreateStudentRole();
        $this->actionInitStudy();
        $this->actionInitContactRequest();

        $this->stdout('RBAC init done!' . PHP_EOL);
    }

    private function initSectionPermission(ManagerInterface $auth)
    {
        $manageSectionsPermission = $auth->createPermission(UserRoles::PERMISSION_MANAGE_SECTIONS);
        $manageSectionsPermission->description = 'Управление разделами';
        $auth->add($manageSectionsPermission);

        $adminRole = $auth->getRole('admin');
        $auth->addChild($adminRole, $manageSectionsPermission);
    }

    public function actionInitSection()
    {
        $auth = Yii::$app->authManager;
        $this->initSectionPermission($auth);
        $this->stdout('RBAC init section done!' . PHP_EOL);
    }

    public function actionCreateStudentRole()
    {
        $auth = Yii::$app->authManager;
        $studentRole = $auth->createRole('student');
        $studentRole->description = 'Ученик';
        $auth->add($studentRole);

        $userRole = $auth->getRole('user');
        $auth->addChild($studentRole, $userRole);
        $this->stdout('RBAC create student role done!' . PHP_EOL);
    }

    public function actionInitStudy()
    {
        $auth = Yii::$app->authManager;
        $teacherRole = $auth->createRole(UserRoles::ROLE_TEACHER);
        $teacherRole->description = 'Учитель';
        $auth->add($teacherRole);

        $manageStudyPermission = $auth->createPermission(UserRoles::PERMISSION_MANAGE_STUDY);
        $manageStudyPermission->description = 'Управление обучением';
        $auth->add($manageStudyPermission);

        $auth->addChild($teacherRole, $manageStudyPermission);

        $moderatorRole = $auth->getRole(UserRoles::ROLE_MODERATOR);
        $auth->addChild($teacherRole, $moderatorRole);

        $adminRole = $auth->getRole(UserRoles::ROLE_ADMIN);
        $auth->removeChild($adminRole, $moderatorRole);
        $auth->addChild($adminRole, $teacherRole);

        $this->stdout('RBAC init study done!' . PHP_EOL);
    }

    public function actionInitContactRequest(): void
    {
        $auth = Yii::$app->authManager;

        $permission = $auth->createPermission(UserRoles::PERMISSION_MANAGE_CONTACT_REQUESTS);
        $permission->description = 'Управление заявками с формы';
        $auth->add($permission);

        $adminRole = $auth->getRole(UserRoles::ROLE_ADMIN);
        $auth->addChild($adminRole, $permission);

        $this->stdout('RBAC init contact request done!' . PHP_EOL);
    }
}

<?php

declare(strict_types=1);

namespace frontend\GetCourse;

use common\models\Profile;
use common\models\User;
use common\models\UserStudent;
use common\rbac\UserRoles;
use common\services\RoleManager;
use common\services\TransactionManager;
use DomainException;
use Yii;
use Yii\base\Exception;

class SignupHandler
{
    /**
     * @var TransactionManager
     */
    private $transactionManager;
    /**
     * @var RoleManager
     */
    private $roleManager;

    public function __construct(TransactionManager $transactionManager, RoleManager $roleManager) {
        $this->transactionManager = $transactionManager;
        $this->roleManager = $roleManager;
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function handle(SignupCommand $command): void {
        $user = User::create(
            User::createUsername(),
            $command->getEmail(),
            Yii::$app->security->generateRandomString()
        );
        $user->updateGetCourseId($command->getGetCourseId());
        $this->transactionManager->wrap(function() use ($user, $command): void {

            if (!$user->save()) {
                throw new DomainException('Ошибка при сохранении пользователя');
            }

            $this->roleManager->assign($user->id, UserRoles::ROLE_USER);

            $student = UserStudent::createMain($user->id, $user->username);
            if (!$student->save()) {
                throw new DomainException('Ошибка при создании ученика');
            }

            $profile = Profile::create($user->id, $command->getFirstName(), $command->getLastName());
            if (!$profile->save()) {
                throw new DomainException('Ошибка при создании профиля пользователя');
            }
        });
    }
}

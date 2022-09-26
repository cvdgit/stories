<?php

declare(strict_types=1);

namespace modules\edu\services;

use common\components\ModelDomainException;
use common\models\Profile;
use common\models\User;
use common\rbac\UserRoles;
use common\services\RoleManager;
use common\services\TransactionManager;

class UserService
{
    private $transactionManager;
    private $roleManager;

    public function __construct(TransactionManager $transactionManager, RoleManager $roleManager)
    {
        $this->transactionManager = $transactionManager;
        $this->roleManager = $roleManager;
    }

    public function createUserProfile(int $userId, string $firstName, string $lastName): void
    {
        $profile = Profile::create($userId, $firstName, $lastName);
        if (!$profile->save()) {
            throw ModelDomainException::create($profile);
        }
    }

    public function createUserForStudent(string $username, string $firstName, string $lastName): void
    {
        $this->transactionManager->wrap(function() use ($username, $firstName, $lastName) {

            $user = User::create(
                $username,
                $username . '@wikids.ru',
                User::createPassword()
            );
            if (!$user->save()) {
                throw ModelDomainException::create($user);
            }

            $this->roleManager->assign($user->id, UserRoles::ROLE_STUDENT);

            $this->createUserProfile($user->id, $firstName, $lastName);
        });
    }
}

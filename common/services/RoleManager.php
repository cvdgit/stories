<?php

declare(strict_types=1);

namespace common\services;

use DomainException;
use Exception;
use yii\rbac\ManagerInterface;

class RoleManager
{
    private $manager;

    public function __construct(ManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @throws Exception
     */
    public function assign(int $userId, string $roleName): void
    {
        if (($role = $this->manager->getRole($roleName)) === null) {
            throw new DomainException('Роль "' . $roleName . '" не существует.');
        }
        $this->manager->revokeAll($userId);
        $this->manager->assign($role, $userId);
    }

    public function revoke(int $userId): bool
    {
        return $this->manager->revokeAll($userId);
    }

    public function canUser(int $userId, string $permissionName, $params = []): bool
    {
        return $this->manager->checkAccess($userId, $permissionName, $params);
    }
}

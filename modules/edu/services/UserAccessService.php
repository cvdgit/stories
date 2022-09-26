<?php

declare(strict_types=1);

namespace modules\edu\services;

use common\components\ModelDomainException;
use modules\edu\models\EduUserAccess;
use yii\db\Query;

class UserAccessService
{
    private function isAccessAlreadyGranted(int $userId): bool
    {
        return (new Query())
            ->from('edu_user_access')
            ->where(['user_id' => $userId])
            ->exists();
    }

    public function createAccess(int $userId): void
    {
        if ($this->isAccessAlreadyGranted($userId)) {
            return;
        }

        $access = EduUserAccess::create($userId);
        if (!$access->save()) {
            throw ModelDomainException::create($access);
        }
    }
}

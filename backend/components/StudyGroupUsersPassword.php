<?php

namespace backend\components;

use common\models\User;

class StudyGroupUsersPassword
{

    private $users;
    private $lines = [];

    public function __construct(array $users)
    {
        $this->users = $users;
    }

    public function create()
    {
        foreach ($this->users as $user) {
            /** @var $user User */
            $password = User::createStudentPassword();
            $this->lines[$user->id] = $this->formatUserLine($user, $password);
            $user->setPassword($password);
            BaseModel::saveModel($user, false);
        }
    }

    private function formatUserLine(User $user, string $password): string
    {
        return sprintf('email: %s , password: %s', $user->email, $password);
    }

    public function getLines(): array
    {
        return $this->lines;
    }
}
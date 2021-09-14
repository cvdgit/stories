<?php

namespace backend\models\study_task;

use common\helpers\EmailHelper;
use common\models\Story;
use common\models\StudyTask;
use common\models\StudyTaskAssign;
use common\models\User;
use common\models\UserToken;

class StudyTaskAssignForm extends StudyTaskAssign
{

    public function sendTokenLoginEmail(User $user, UserToken $userToken, StudyTask $task = null): void
    {
        $response = EmailHelper::sendEmail($user->email, 'Вам назначен электронный курс на Wikids', 'tokenLogin-html', [
            'user' => $user,
            'userToken' => $userToken,
            'task' => $task,
        ]);
        if (!$response->isSuccess()) {
            throw new \DomainException('TokenLoginEmail sent error');
        }
    }

    public function assign(): void
    {
        if (!$this->validate()) {
            throw new \DomainException(implode(PHP_EOL, $this->getErrorSummary(true)));
        }

        $this->save();
        $this->refresh();

        $group = $this->studyGroup;
        $task = $this->studyTask;
        foreach ($group->users as $user) {

            $userToken = UserToken::create($user->id);
            $userToken->save();

            $this->sendTokenLoginEmail($user, $userToken, $task);
        }
    }
}
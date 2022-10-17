<?php

declare(strict_types=1);

namespace modules\edu\services;

use DomainException;
use modules\edu\forms\teacher\ParentInviteForm;
use modules\edu\models\EduParentInvite;
use modules\edu\models\EduUser;
use yii\mail\MailerInterface;

class ParentInviteService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendInvite(int $studentId, ParentInviteForm $form): void
    {
        if (EduUser::findOne(['email' => $form->email, 'status' => 10]) === null) {
            throw new DomainException('Пользователь с таким email не зарегистрирован на Wikids');
        }

        if (EduParentInvite::findOne(['email' => $form->email, 'student_id' => $studentId]) !== null) {
            throw new DomainException('Приглашение уже существует');
        }

        $invite = EduParentInvite::createInvite($form->email, $studentId);
        if (!$invite->save()) {
            throw new DomainException('EduParentStudent save exception');
        }

        $invite->sendParentInvite($this->mailer);
    }
}

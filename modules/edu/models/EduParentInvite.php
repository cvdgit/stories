<?php

declare(strict_types=1);

namespace modules\edu\models;

use common\helpers\EmailHelper;
use DomainException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\mail\MailerInterface;

/**
 * @property int $id [int(11)]
 * @property string $email [varchar(255)]
 * @property int $student_id [int(11)]
 * @property int $created_at [int(11)]
 * @property int $status [tinyint(3)]
 * @property string $code [varchar(255)]
 */
class EduParentInvite extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%edu_parent_invite}}';
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
        ];
    }

    public static function createInvite(string $email, int $studentId): self
    {
        $model = new self();
        $model->email = $email;
        $model->student_id = $studentId;
        $model->status = 0;
        $model->code = sprintf("%09d", random_int(1, 999999999));
        return $model;
    }

    public function sendParentInvite(MailerInterface $mailer): void
    {
        if ($this->status !== 0) {
            throw new DomainException('Некорректный статус для отправки приглашения');
        }
        /*$mail = $mailer
            ->compose(['html' => '@common/mail/parent-invite'], ['code' => $this->code])
            ->setSubject('Приглашение на wikids.ru')
            ->setTo($this->email);
        if (!$mail->send()) {
            throw new DomainException('Unable to send email ' . $this->email);
        }*/

        $response = EmailHelper::sendEmail($this->email, 'Приглашение на wikids.ru', 'parent-invite', ['code' => $this->code]);
        if (!$response->isSuccess()) {
            throw new DomainException('Unable to send email ' . $this->email);
        }
    }

    public function isActive(): bool
    {
        return $this->status === 1;
    }

    public function setInviteActive(): void
    {
        $this->status = 1;
    }

    public function isOwnerEmail(string $email): bool
    {
        return strcmp($this->email, $email) === 0;
    }
}

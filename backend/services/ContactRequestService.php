<?php

declare(strict_types=1);

namespace backend\services;

use backend\forms\ContactRequestCommentForm;
use Yii;

class ContactRequestService
{
    public function updateComment(int $contactId, ContactRequestCommentForm $form): void
    {
        Yii::$app->db->createCommand()
            ->update('contact_request', [
                'comment' => $form->comment,
                'updated_at' => time(),
            ], ['id' => $contactId])
            ->execute();
    }
}

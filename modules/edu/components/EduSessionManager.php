<?php

namespace modules\edu\components;

use Ramsey\Uuid\Uuid;
use Yii;
use yii\web\Cookie;

class EduSessionManager
{

    public function switch(int $userId, int $studentId, bool $parent = false): void
    {
        $readCookies = Yii::$app->response->cookies;
        if ($readCookies->has('uid')) {
            $readCookies->remove('uid');
        }

        Yii::$app->db->createCommand()
            ->delete('user_student_session', 'user_id = :user AND student_id = :student', [':user' => $userId, ':student' => $studentId])
            ->execute();

        $uid = Uuid::uuid4()->toString();
        Yii::$app->response->cookies->add(new Cookie([
            'name' => 'uid',
            'value' => $uid,
            'expire' => $parent ? 0 : time() + 60 * 60 * 24 * 265,
        ]));

        Yii::$app->db->createCommand()
            ->insert('user_student_session', [
                'uid' => $uid,
                'user_id' => $userId,
                'student_id' => $studentId,
            ])
            ->execute();
    }
}

<?php

namespace modules\edu\components;

use Ramsey\Uuid\Uuid;
use Yii;
use yii\web\Cookie;

class EduSessionManager
{

    public function switch(int $userId, int $studentId, bool $parent = false): void
    {

        $writeCookies = Yii::$app->response->cookies;
        if ($writeCookies->has('uid')) {
            $uidCookie = $writeCookies->get('uid');
            $writeCookies->remove($uidCookie);
        }

        $uid = Uuid::uuid4()->toString();
        $writeCookies->add(new Cookie([
            'name' => 'uid',
            'value' => $uid,
            'expire' => $parent ? 0 : 60 * 60 * 24 * 265
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

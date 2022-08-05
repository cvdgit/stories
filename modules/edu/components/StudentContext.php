<?php

declare(strict_types=1);


namespace modules\edu\components;

use common\models\UserStudent;
use Yii;
use yii\base\Component;
use yii\db\Query;

class StudentContext extends Component
{

    private $cookieUid;
    private $student;

    public function init()
    {
        parent::init();

        $this->cookieUid = Yii::$app->request->cookies->getValue('uid');
    }

    public function haveStudentCookie(): bool
    {
        return $this->cookieUid !== null;
    }

    private function findStudent(string $uid): ?UserStudent
    {
        $sessionRow = (new Query())
            ->select('*')
            ->from('user_student_session')
            ->where('uid = :uid', [':uid' => $uid])
            ->one();
        if ($sessionRow !== false) {
            return UserStudent::findOne($sessionRow['student_id']);
        }
    }

    public function getStudent(): ?UserStudent
    {
        if ($this->student === null && $this->haveStudentCookie()) {
            $this->student = $this->findStudent($this->cookieUid);
        }
        return $this->student;
    }
}

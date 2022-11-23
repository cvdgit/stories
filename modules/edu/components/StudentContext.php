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

    public function init(): void
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
            $studentId = (int)$sessionRow['student_id'];
            return UserStudent::findOne($studentId);
        }
        return null;
    }

    private function checkUserOwnsStudent(int $userId, int $studentId): bool
    {
        return (new Query())
            ->from('user_student')
            ->where(['id' => $studentId])
            ->andWhere(['user_id' => $userId])
            ->exists();
    }

    public function getStudent(): ?UserStudent
    {
        if ($this->student === null && $this->haveStudentCookie()) {

            if (($student = $this->findStudent($this->cookieUid)) === null) {
                return null;
            }

            $userId = Yii::$app->user->getId();
            if ($this->checkUserOwnsStudent($userId, $student->id)) {
                $this->student = $student;
            }
        }
        return $this->student;
    }

    public function getId(): ?int
    {
        return $this->getStudent() !== null ? $this->getStudent()->id : null;
    }
}

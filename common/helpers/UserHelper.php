<?php

namespace common\helpers;

use common\models\UserStudent;
use Yii;
use yii\helpers\ArrayHelper;
use common\models\User;

class UserHelper
{

    public static function getUserArray()
    {
        return ArrayHelper::map(User::find()->all(), 'id', 'username');
    }

    public static function getStatusArray()
    {
        return [
            User::STATUS_DELETED => 'Удален',
            User::STATUS_WAIT => 'Ожидание подтверждения',
            User::STATUS_ACTIVE => 'Активен',
        ];
    }

    public static function getStatusText($status)
    {
        $arr = static::getStatusArray();
        return isset($arr[$status]) ? $arr[$status] : '';
    }

    public static function getCurrentUserStudentID()
    {
        $webUser = Yii::$app->user;
        if ($webUser->isGuest) {
            return null;
        }
        return $webUser->identity->getStudentID();
    }

    public static function getStudent(int $id = null)
    {
        $webUser = Yii::$app->user;
        if ($webUser->isGuest) {
            return null;
        }
        if ($id === null) {
            return $webUser->identity->student();
        }
        foreach ($webUser->identity->students as $student) {
            if ($student->id === $id) {
                return $student;
            }
        }
    }

    public static function getStudents()
    {
        $students = [];
        if (!Yii::$app->user->isGuest) {
            $user = Yii::$app->user->identity;
            foreach ($user->students as $student) {
                /** @var $student UserStudent */
                $students[] = [
                    'id' => $student->id,
                    'name' => $student->getStudentName(),
                ];
            }
        }
        return $students;
    }

}

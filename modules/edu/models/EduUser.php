<?php

declare(strict_types=1);

namespace modules\edu\models;

use common\models\Profile;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id [int(11)]
 * @property string $username [varchar(255)]
 * @property string $auth_key [varchar(32)]
 * @property string $password_hash [varchar(255)]
 * @property string $password_reset_token [varchar(255)]
 * @property string $email [varchar(255)]
 * @property string $email_confirm_token [varchar(255)]
 * @property int $status [smallint(6)]
 * @property int $group [smallint(6)]
 * @property int $created_at [int(11)]
 * @property int $updated_at [int(11)]
 * @property string $image [varchar(64)]
 * @property int $last_activity [int(11)]
 *
 * @property EduStudent[] $students
 * @property Profile|null $profile
 */
class EduUser extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'user';
    }

    public function getProfile(): ActiveQuery
    {
        return $this->hasOne(Profile::class, ['user_id' => 'id']);
    }

    public function getProfileName(): string
    {
        if ($this->profile !== null) {
            return $this->profile->getFullName();
        }
        return $this->username;
    }

    public function getProfilePhoto(): string
    {
        $noAvatar = '/img/avatar.png';
        if ($this->profile !== null) {
            $profilePhoto = $this->profile->profilePhoto;
            if ($profilePhoto !== null) {
                return $profilePhoto->getThumbFileUrl('file', 'list', $noAvatar);
            }
        }
        return $noAvatar;
    }

    public function getStudents(): ActiveQuery
    {
        return $this->hasMany(EduStudent::class, ['id' => 'student_id'])
            ->viaTable('edu_parent_student', ['parent_id' => 'id']);
    }

    public static function createUsername(): string
    {
        return 'user' . sprintf("%06d", random_int(1, 999999));
    }
}

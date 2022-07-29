<?php

namespace modules\edu\models;

use common\models\UserStudent;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "student_login".
 *
 * @property int $student_id
 * @property string $username
 * @property string $password
 *
 * @property UserStudent $student
 */
class StudentLogin extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'student_login';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['student_id', 'username', 'password'], 'required'],
            [['student_id'], 'integer'],
            [['username', 'password'], 'string', 'max' => 50],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserStudent::class, 'targetAttribute' => ['student_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'student_id' => 'Student ID',
            'username' => 'Username',
            'password' => 'Password',
        ];
    }

    public function getStudent(): ActiveQuery
    {
        return $this->hasOne(UserStudent::class, ['id' => 'student_id']);
    }

    public static function findLogin(string $login): ?self
    {
        return self::find()->where(['username' => $login])->one();
    }

    public function validatePassword($password): bool
    {
        return Yii::$app->security->compareString($this->password, $password);
    }
}

<?php

namespace common\models;

use DomainException;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $email_confirm_token
 * @property string $auth_key
 * @property integer $status
 * @property integer $group
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 * @property string $image
 *
 * @property Comment[] $comments
 * @property Payment[] $payments
 * @property Profile $profile
 * @property Story[] $stories
 */
class User extends ActiveRecord implements IdentityInterface
{

    const STATUS_DELETED = 0;
    const STATUS_WAIT = 5;
    const STATUS_ACTIVE = 10;

    const GROUP_ADMIN = 1;
    const GROUP_AUTHOR = 2;

    public $active_payment;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ИД',
            'username' => 'Имя пользователя',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата изменения',
            'status' => 'Статус',
            'active_payment' => 'Подписка',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function generateEmailConfirmToken()
    {
        $this->email_confirm_token = Yii::$app->security->generateRandomString();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * @return ActiveQuery
     */
    public function getPayments(): ActiveQuery
    {
        return $this->hasMany(Payment::class, ['user_id' => 'id'])->with('rate')->orderBy(['created_at' => SORT_DESC]);
    }

    /**
     * @return ActiveQuery
     */
    public function getActivePayment(): ActiveQuery
    {
        return $this->hasOne(Payment::class, ['user_id' => 'id'])->validPayments();
    }

    public function hasSubscription()
    {
        return !empty($this->activePayment);
    }

    public static function findModel($id): self
    {
        if (($model = self::findOne($id)) !== null) {
            return $model;
        }
        throw new DomainException('Пользователь не найден.');
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public static function createSignup(string $username, string $email, string $password)
    {
        $user = new self();
        $user->username = $username;
        $user->email = $email;
        $user->status = self::STATUS_WAIT;
        $user->group = self::GROUP_AUTHOR;
        $user->setPassword($password);
        $user->generateAuthKey();
        $user->generateEmailConfirmToken();
        return $user;
    }

    public static function findByUsernameOrEmail($value)
    {
        return self::find()->andWhere(['or', ['username' => $value], ['email' => $value]])->one();
    }

    /**
     * @param string $username
     * @param string $email
     * @param string $password
     * @return User
     * @throws yii\base\Exception
     */
    public static function create(string $username, string $email, string $password): User
    {
        $user = new self();
        $user->username = $username;
        $user->email = $email;
        $user->setPassword(!empty($password) ? $password : Yii::$app->security->generateRandomString());
        $user->generateAuthKey();
        $user->status = self::STATUS_ACTIVE;
        return $user;
    }

    public function edit(string $username, string $email, int $status): void
    {
        $this->username = $username;
        $this->email = $email;
        $this->status = $status;
    }

    /**
     * @return yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::class, ['user_id' => 'id']);
    }

    /**
     * @return yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::class, ['user_id' => 'id']);
    }

    /**
     * @return yii\db\ActiveQuery
     */
    public function getStories()
    {
        return $this->hasMany(Story::class, ['user_id' => 'id']);
    }

    public function getProfileName()
    {
        if ($this->profile !== null) {
            return $this->profile->getFullName();
        }
        return $this->username;
    }

    public function updateProfile(string $firstName, string $lastName)
    {
        $profile = $this->profile;
        if ($profile === null) {
            $profile = Profile::createProfile($this->id);
        }
        $profile->first_name = $firstName;
        $profile->last_name = $lastName;
        return $profile;
    }

}

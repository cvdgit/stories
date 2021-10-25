<?php

namespace common\models;

use common\services\RoleManager;
use DomainException;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
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
 * @property integer last_activity
 *
 * @property Comment[] $comments
 * @property Payment[] $payments
 * @property Profile $profile
 * @property Story[] $stories
 * @property Notification[] $notifications
 * @property UserStudent[] $students
 */
class User extends ActiveRecord implements IdentityInterface
{

    const STATUS_DELETED = 0;
    const STATUS_WAIT = 5;
    const STATUS_ACTIVE = 10;

    const GROUP_ADMIN = 1;
    const GROUP_AUTHOR = 2;

    public $active_payment;

    private $roleManager;

    public function __construct($config = [])
    {
        $this->roleManager = Yii::createObject(RoleManager::class);
        parent::__construct($config);
    }

    public static function tableName()
    {
        return '{{%user}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public static function find()
    {
        return new UserQuery(static::class);
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ИД',
            'username' => 'Имя пользователя',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата изменения',
            'status' => 'Статус',
            'active_payment' => 'Подписка',
            'last_activity' => 'Последняя активность',
        ];
    }

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

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

    public static function findByEmail(string $email): ?self
    {
        return static::findOne(['email' => $email]);
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

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

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
     * @return \yii\db\ActiveQuery
     */
    public function getAuth()
    {
        return $this->hasOne(Auth::class, ['user_id' => 'id']);
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

    public static function createSignup($username, $email, $password)
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

    public function getProfilePhoto()
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

    public static function updateLastActivity()
    {
        if (!Yii::$app->user->isGuest) {
            self::updateAll(['last_activity' => time()], 'id = :id', [':id' => Yii::$app->user->id]);
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotifications()
    {
        return $this->hasMany(Notification::class, ['id' => 'notification_id'])
            ->viaTable('user_notification', ['user_id' => 'id']);
    }

    public function getUnreadUserNotificationCount()
    {
        return $this->getNotifications()->unreadCount($this->id);
    }

    public function getUnreadUserNotification()
    {
        return $this->getNotifications()->unread($this->id)->all();
    }

    public function getLastUserNotification()
    {
        return $this->getNotifications()->last($this->id)->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudents()
    {
        return $this->hasMany(UserStudent::class, ['user_id' => 'id']);
    }

    public function getStudentsAsArray()
    {
        return $this->getStudents()->andWhere('status = 0')->asArray()->all();
    }

    public function createMainStudent()
    {
        $student = UserStudent::createMain($this->id, $this->username);
        $student->save();
    }

    public function student()
    {
        return $this
            ->getStudents()
            ->andWhere('status = :status', [':status' => UserStudent::STATUS_MAIN])
            ->one();
    }

    public function getStudentID()
    {
        return $this->student()->id;
    }

    public static function getUserList(): array
    {
        return ArrayHelper::map(self::find()->all(), 'id', 'profileName');
    }

    public static function createUsername(): string
    {
        return 'user' . sprintf("%06d", random_int(1, 999999));
    }

    public static function createPassword(): string
    {
        return Yii::$app->security->generateRandomString();
    }

    public static function createStudentPassword(): string
    {
        return Yii::$app->security->generateRandomString(8);
    }

    public function afterDelete()
    {
        $this->roleManager->revoke($this->id);
        parent::afterDelete();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudyGroups()
    {
        return $this->hasMany(StudyGroup::class, ['id' => 'study_group_id'])
            ->viaTable('study_group_user', ['user_id' => 'id']);
    }
}

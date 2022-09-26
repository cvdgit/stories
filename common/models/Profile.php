<?php

namespace common\models;

use DomainException;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

/**
 * This is the model class for table "profile".
 *
 * @property int $id
 * @property int $photo_id
 * @property int $user_id
 * @property string $first_name
 * @property string $last_name
 * @property string $gender
 * @property int $created_at
 * @property int $updated_at
 *
 * @property string $fullName
 * @property ProfileImage $profilePhoto
 * @property User $user
 */
class Profile extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%profile}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            'saveRelations' => [
                'class' => SaveRelationsBehavior::class,
                'relations' => [
                    'profilePhoto',
                ],
            ],
        ];
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'first_name', 'last_name'], 'required'],
            [['user_id', 'photo_id'], 'integer'],
            [['first_name', 'last_name'], 'string', 'max' => 50],
            [['gender'], 'string', 'max' => 1],
            [['user_id'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'gender' => 'Gender',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getFullName(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public static function create(int $userId, string $firstName, string $lastName): self
    {
        $model = new static();
        $model->user_id = $userId;
        $model->first_name = $firstName;
        $model->last_name = $lastName;
        return $model;
    }

    public static function createProfile($userID)
    {
        $profile = new static();
        $profile->user_id = $userID;
        return $profile;
    }

    public static function findModel($id): self
    {
        if (($model = self::findOne($id)) !== null) {
            return $model;
        }
        throw new DomainException('Профиль пользователя не найден.');
    }

    /**
     * @return ActiveQuery
     */
    public function getProfilePhoto()
    {
        return $this->hasOne(ProfileImage::class, ['id' => 'photo_id']);
    }

    public function addProfilePhoto(UploadedFile $photo): void
    {
        if ($this->profilePhoto === null) {
            $model = ProfileImage::create($photo);
        }
        else {
            $model = $this->profilePhoto;
            $model->file = $photo;
        }
        $this->profilePhoto = $model;
    }

}

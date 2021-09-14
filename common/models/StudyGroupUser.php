<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "study_group_user".
 *
 * @property int $study_group_id
 * @property int $user_id
 *
 * @property StudyGroup $studyGroup
 * @property User $user
 */
class StudyGroupUser extends ActiveRecord
{

    public static function tableName()
    {
        return 'study_group_user';
    }

    public function rules()
    {
        return [
            [['study_group_id', 'user_id'], 'required'],
            [['study_group_id', 'user_id'], 'integer'],
            [['study_group_id', 'user_id'], 'unique', 'targetAttribute' => ['study_group_id', 'user_id']],
            [['study_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => StudyGroup::class, 'targetAttribute' => ['study_group_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'study_group_id' => 'Study Group ID',
            'user_id' => 'User ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudyGroup()
    {
        return $this->hasOne(StudyGroup::class, ['id' => 'study_group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public static function create(int $groupID, int $userID): self
    {
        $model = new self();
        $model->study_group_id = $groupID;
        $model->user_id = $userID;
        return $model;
    }

    public static function deleteAllByGroup(int $group_id): void
    {
        self::deleteAll('study_group_id = :group', [':group' => $group_id]);
    }

    public static function findItem(int $groupID, int $userID): ?self
    {
        return self::find()
            ->where('study_group_id = :group AND user_id = :user', [':group' => $groupID, ':user' => $userID])
            ->one();
    }

    public function afterSave($insert, $changedAttributes)
    {
        StudyGroup::updateRecord($this->study_group_id);
        parent::afterSave($insert, $changedAttributes);
    }

    public function afterDelete()
    {
        StudyGroup::updateRecord($this->study_group_id);
        parent::afterDelete();
    }
}
